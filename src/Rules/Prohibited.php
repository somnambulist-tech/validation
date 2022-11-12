<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Based on Laravel validators prohibited
 */
class Prohibited extends Rule
{
    protected string $message = 'rule.prohibited';

    public function check(mixed $value): bool
    {
        return false;
    }
}
