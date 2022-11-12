<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Helper;
use Somnambulist\Components\Validation\Rule;

class In extends Rule
{
    protected string $message = 'rule.in';
    protected bool $strict = false;

    public static function make(array $values): string
    {
        return sprintf('in:%s', Helper::flattenValues($values));
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

    public function strict(bool $strict = true): self
    {
        $this->strict = $strict;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['allowed_values']);

        return in_array($value, $this->parameter('allowed_values'), $this->strict);
    }
}
