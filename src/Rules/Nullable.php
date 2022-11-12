<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Nullable extends Rule
{
    public function check(mixed $value): bool
    {
        return true;
    }
}
