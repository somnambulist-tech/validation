<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

class Sometimes extends Required
{
    public function check(mixed $value): bool
    {
        return true;
    }
}
