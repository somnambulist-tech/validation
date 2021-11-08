<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Fixtures;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Even
 *
 * @package    Somnambulist\Components\Validation\Tests\Fixtures
 * @subpackage Somnambulist\Components\Validation\Tests\Fixtures\Even
 */
class Even extends Rule
{
    protected string $message = "The :attribute must be even";

    public function check($value): bool
    {
        if (! is_numeric($value)) {
            return false;
        }

        return $value % 2 === 0;
    }
}
