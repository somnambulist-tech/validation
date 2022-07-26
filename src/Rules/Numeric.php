<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Numeric extends Rule
{
    protected string $message = 'rule.numeric';

    public function check($value): bool
    {
        return is_numeric($value);
    }
}
