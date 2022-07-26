<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Fixtures;

use Somnambulist\Components\Validation\Rule;

class Unique extends Rule
{
    protected string $message = "The :attribute must be unique";

    public function check($value): bool
    {
        return false;
    }
}
