<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class TypeArray extends Rule
{
    protected string $message = 'rule.array';

    public function check(mixed $value): bool
    {
        return is_array($value);
    }
}
