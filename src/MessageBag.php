<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use Somnambulist\Components\Validation\Exceptions\MessageException;

class MessageBag
{
    private array $messages;
    private string $defaultLang = 'en';

    public function __construct()
    {
    }

    public function default(string $lang): self
    {
        $this->defaultLang = $lang;

        return $this;
    }

    public function all(string $lang): array
    {
        return $this->messages[$lang] ?? [];
    }

    public function add(string $lang, array $messages): self
    {
        foreach ($messages as $key => $message) {
            $this->replace($lang, $key, $message);
        }

        return $this;
    }

    public function replace(string $lang, string $key, string $message): self
    {
        $this->messages[$lang][$key] = $message;

        return $this;
    }

    public function firstOf(array $keys, string $lang = null): string
    {
        foreach ($keys as $key) {
            if ($this->has($key, $lang)) {
                return $this->get($key, $lang);
            }
        }

        throw MessageException::noMessageForKeys($lang ?? $this->defaultLang, $keys);
    }

    public function get(string $key, string $lang = null): ?string
    {
        return $this->messages[$lang ?? $this->defaultLang][$key] ?? null;
    }

    public function has(string $key, string $lang = null): bool
    {
        return isset($this->messages[$lang ?? $this->defaultLang][$key]);
    }

    public function hasAnyOf(array $keys, string $lang = null): bool
    {
        foreach ($keys as $key) {
            if ($this->has($key, $lang)) {
                return true;
            }
        }

        return false;
    }
}
