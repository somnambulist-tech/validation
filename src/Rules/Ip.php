<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Ip extends Rule
{
    protected string $message = 'rule.ip';

    public function check(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}
