<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Fixtures;

use Somnambulist\Components\Validation\Rule;

class Required extends Rule
{
    public function check($value): bool
    {
        return true;
    }
}
