<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Fixtures;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Unique
 *
 * @package    Somnambulist\Components\Validation\Tests\Fixtures
 * @subpackage Somnambulist\Components\Validation\Tests\Fixtures\Unique
 */
class Unique extends Rule
{
    protected string $message = "The :attribute must be unique";

    public function check($value): bool
    {
        return false;
    }
}
