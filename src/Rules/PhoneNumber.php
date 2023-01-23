<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

use function preg_match;

class PhoneNumber extends Rule
{
    protected string $message = 'rule.phone_number';

    public function check(mixed $value): bool
    {
        return 1 === preg_match('/^\+?[1-9]\d{1,14}$/', (string)$value);
    }
}
