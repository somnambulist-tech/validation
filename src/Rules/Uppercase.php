<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Uppercase extends Rule
{
    protected string $message = 'rule.uppercase';

    public function check($value): bool
    {
        return mb_strtoupper($value, mb_detect_encoding($value)) === $value;
    }
}
