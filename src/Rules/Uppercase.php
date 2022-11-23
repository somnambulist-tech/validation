<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Uppercase extends Rule
{
    protected string $message = 'rule.uppercase';

    public function check(mixed $value): bool
    {
        return mb_strtoupper((string)$value, mb_detect_encoding((string)$value)) === (string)$value;
    }
}
