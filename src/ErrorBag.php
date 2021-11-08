<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

/**
 * Class ErrorBag
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\ErrorBag
 */
class ErrorBag
{
    private array $messages;

    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    public function add(string $key, string $rule, string $message): void
    {
        $this->messages[$key][$rule] = $message;
    }

    public function count(): int
    {
        return count($this->all());
    }

    public function all(string $format = ':message'): array
    {
        $messages = $this->messages;
        $results  = [];

        foreach ($messages as $key => $keyMessages) {
            foreach ($keyMessages as $message) {
                $results[] = $this->formatMessage($message, $format);
            }
        }

        return $results;
    }

    protected function formatMessage(string $message, string $format): string
    {
        return str_replace(':message', $message, $format);
    }

    public function has(string $key): bool
    {
        [$key, $ruleName] = $this->parsekey($key);

        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);

            return count(Helper::arrayDot($messages)) > 0;
        } else {
            $messages = $this->messages[$key] ?? null;

            if (!$ruleName) {
                return !empty($messages);
            } else {
                return !empty($messages) and isset($messages[$ruleName]);
            }
        }
    }

    protected function parseKey(string $key): array
    {
        $expl     = explode(':', $key, 2);
        $key      = $expl[0];
        $ruleName = $expl[1] ?? null;

        return [$key, $ruleName];
    }

    protected function isWildcardKey(string $key): bool
    {
        return str_contains($key, '*');
    }

    protected function filterMessagesForWildcardKey(string $key, $ruleName = null): array
    {
        $messages = $this->messages;
        $pattern  = preg_quote($key, '#');
        $pattern  = str_replace('\*', '.*', $pattern);

        $filteredMessages = [];

        foreach ($messages as $k => $keyMessages) {
            if ((bool)preg_match('#^' . $pattern . '\z#u', $k) === false) {
                continue;
            }

            foreach ($keyMessages as $rule => $message) {
                if ($ruleName and $rule != $ruleName) {
                    continue;
                }
                $filteredMessages[$k][$rule] = $message;
            }
        }

        return $filteredMessages;
    }

    public function first(string $key): mixed
    {
        [$key, $ruleName] = $this->parsekey($key);

        if ($this->isWildcardKey($key)) {
            $messages        = $this->filterMessagesForWildcardKey($key, $ruleName);
            $flattenMessages = Helper::arrayDot($messages);

            return array_shift($flattenMessages);
        } else {
            $keyMessages = $this->messages[$key] ?? [];

            if (empty($keyMessages)) {
                return null;
            }

            if ($ruleName) {
                return $keyMessages[$ruleName] ?? null;
            } else {
                return array_shift($keyMessages);
            }
        }
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
            $keyMessages = $this->messages[$key] ?? [];

            foreach ($keyMessages as $rule => $message) {
                if ($ruleName and $ruleName != $rule) {
                    continue;
                }
                $results[$rule] = $this->formatMessage($message, $format);
            }
        }

        return $results;
    }

    public function firstOfAll(string $format = ':message', bool $dotNotation = false): array
    {
        $messages = $this->messages;
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
        return $this->messages;
    }
}
