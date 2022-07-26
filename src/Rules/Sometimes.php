<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

class Sometimes extends Required
{
    public function check($value): bool
    {
        return true;
    }
}
