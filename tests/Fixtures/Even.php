<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Fixtures;

use Somnambulist\Components\Validation\Rule;

class Even extends Rule
{
    protected string $message = 'rule.even';

    public function check($value): bool
    {
        if (! is_numeric($value)) {
            return false;
        }

        return $value % 2 === 0;
    }
}
