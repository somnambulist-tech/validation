<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

use function in_array;

class TypeBoolean extends Rule
{
    protected string $message = 'rule.boolean';

    public function check(mixed $value): bool
    {
        return in_array($value, [true, false, "true", "false", 1, 0, "0", "1", "y", "n"], true);
    }
}
