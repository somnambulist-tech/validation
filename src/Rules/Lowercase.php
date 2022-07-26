<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Lowercase extends Rule
{
    protected string $message = 'rule.lowercase';

    public function check($value): bool
    {
        return mb_strtolower($value, mb_detect_encoding($value)) === $value;
    }
}
