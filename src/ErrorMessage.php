<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use function array_merge;
use function is_array;
use function is_numeric;
use function is_object;
use function is_string;
use function json_encode;
use function str_starts_with;
use function strtr;

class ErrorMessage
{
    private ?string $message = null;

    public function __construct(
        private string $key,
        private array $params = []
    ) {
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return strtr($this->message ?? $this->key, $this->toArrayOfStrings($this->params));
    }

    public function key(): string
    {
        return $this->key;
    }

    public function addParam(string $key, mixed $value): self
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function addParams(array $params): self
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    private function toArrayOfStrings(array $params): array
    {
        $ret = [];

        foreach ($params as $key => $value) {
            $prefix = (str_starts_with($key, '[') || str_starts_with($key, '{')) ? '' : ':';
            $ret[$prefix . $key] = $this->stringify($value);
        }

        return $ret;
    }

    private function stringify(mixed $value): string
    {
        return match (true) {
            is_string($value), is_numeric($value) => (string)$value,
            is_array($value) && Helper::arrayIsList($value) && !Helper::arrayIsNested($value) => Helper::join(Helper::wraps($value, '"'), ', ', ', '),
            is_array($value) || is_object($value) => json_encode($value),

            default => '',
        };
    }
}
