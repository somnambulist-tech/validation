<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Lowercase extends Rule
{
    protected string $message = 'rule.lowercase';

    public function check(mixed $value): bool
    {
        return mb_strtolower((string)$value, mb_detect_encoding((string)$value)) === (string)$value;
    }
}
