<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Fixtures;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Required
 *
 * @package    Somnambulist\Components\Validation\Tests\Fixtures
 * @subpackage Somnambulist\Components\Validation\Tests\Fixtures\Required
 */
class Required extends Rule
{
    public function check($value): bool
    {
        return true;
    }
}
