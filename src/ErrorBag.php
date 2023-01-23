<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use ArrayIterator;
use Countable;
use IteratorAggregate;

use function count;
use function explode;
use function preg_match;
use function preg_quote;
use function str_contains;
use function str_replace;

class ErrorBag implements Countable, IteratorAggregate
{
    public function __construct(private array $errors = [])
    {
        foreach ($errors as $key => $rules) {
            foreach ($rules as $rule => $error) {
                $this->add($key, $rule, $error);
            }
        }
    }

    public function add(string $key, string $rule, ErrorMessage $message): void
    {
        $this->errors[$key][$rule] = $message;
    }

    public function count(): int
    {
        return count($this->all());
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->all());
    }

    public function all(string $format = ':message'): array
    {
        $results  = [];

        foreach ($this->errors as $keyMessages) {
            foreach ($keyMessages as $message) {
                $results[] = $this->formatMessage($message, $format);
            }
        }

        return $results;
    }

    private function formatMessage(ErrorMessage $message, string $format): string
    {
        return str_replace(':message', (string)$message, $format);
    }

    public function has(string $key): bool
    {
        [$key, $ruleName] = $this->parsekey($key);

        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);

            return count(Helper::arrayDot($messages)) > 0;
        } else {
            $messages = $this->errors[$key] ?? null;

            if (!$ruleName) {
                return !empty($messages);
            } else {
                return !empty($messages) && isset($messages[$ruleName]);
            }
        }
    }

    private function parseKey(string $key): array
    {
        $expl     = explode(':', $key, 2);
        $key      = $expl[0];
        $ruleName = $expl[1] ?? null;

        return [$key, $ruleName];
    }

    private function isWildcardKey(string $key): bool
    {
        return str_contains($key, '*');
    }

    private function filterMessagesForWildcardKey(string $key, $ruleName = null): array
    {
        $messages = $this->errors;
        $pattern  = preg_quote($key, '#');
        $pattern  = str_replace('\*', '.*', $pattern);

        $filteredMessages = [];

        foreach ($messages as $k => $keyMessages) {
            if ((bool)preg_match('#^' . $pattern . '\z#u', $k) === false) {
                continue;
            }

            foreach ($keyMessages as $rule => $message) {
                if ($ruleName && $rule != $ruleName) {
                    continue;
                }
                $filteredMessages[$k][$rule] = $message;
            }
        }

        return $filteredMessages;
    }

    public function first(string $key): ?string
    {
        [$key, $ruleName] = $this->parsekey($key);

        if ($this->isWildcardKey($key)) {
            $messages        = $this->filterMessagesForWildcardKey($key, $ruleName);
            $flattenMessages = Helper::arrayDot($messages);

            $ret = array_shift($flattenMessages);
        } else {
            $keyMessages = $this->errors[$key] ?? [];

            if (empty($keyMessages)) {
                return null;
            }

            if ($ruleName) {
                $ret = $keyMessages[$ruleName] ?? null;
            } else {
                $ret = array_shift($keyMessages);
            }
        }

        return !is_null($ret) ? (string)$ret : null;
    }

    public function get(string $key, string $format = ':message'): array
    {
        [$key, $ruleName] = $this->parsekey($key);

        $results = [];

        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);

            foreach ($messages as $explicitKey => $keyMessages) {
                foreach ($keyMessages as $rule => $message) {
                    $results[$explicitKey][$rule] = $this->formatMessage($message, $format);
                }
            }
        } else {
            $keyMessages = $this->errors[$key] ?? [];

            foreach ($keyMessages as $rule => $message) {
                if ($ruleName && $ruleName != $rule) {
                    continue;
                }

                $results[$rule] = $this->formatMessage($message, $format);
            }
        }

        return $results;
    }

    public function firstOfAll(string $format = ':message', bool $dotNotation = false): array
    {
        $messages = $this->errors;
        $results  = [];

        foreach ($messages as $key => $keyMessages) {
            if ($dotNotation) {
                $results[$key] = $this->formatMessage(array_shift($messages[$key]), $format);
            } else {
                Helper::arraySet($results, $key, $this->formatMessage(array_shift($messages[$key]), $format));
            }
        }

        return $results;
    }

    public function toArray(): array
    {
        return $this->errors;
    }

    public function toDataBag(): DataBag
    {
        return new DataBag($this->errors);
    }
}
