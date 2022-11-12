<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class TypeString extends Rule
{
    protected string $message = 'rule.string';

    public function check(mixed $value): bool
    {
        return is_string($value);
    }
}
