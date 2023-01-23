<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Helper;
use Somnambulist\Components\Validation\Rule;

use function explode;
use function in_array;

class AnyOf extends Rule
{
    protected string $message = 'rule.any_of';
    protected bool $strict = false;

    public static function make(array $values): string
    {
        return sprintf('any_of:%s', Helper::flattenValues($values));
    }

    public function fillParameters(array $params): self
    {
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }

        $this->params['allowed_values'] = $params;

        return $this;
    }

    public function values(array $values): self
    {
        $this->params['allowed_values'] = $values;

        return $this;
    }

    public function separator(string $char): self
    {
        $this->params['separator'] = $char;

        return $this;
    }

    public function strict(bool $strict = true): self
    {
        $this->strict = $strict;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['allowed_values']);

        $valid  = true;
        $values = is_string($value) ? explode($this->parameter('separator', ','), $value) : (array)$value;

        foreach ($values as $v) {
            $valid = $valid && in_array($v, $this->parameter('allowed_values'), $this->strict);
        }

        return $valid;
    }
}
