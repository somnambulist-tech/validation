<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Traits;

use Exception;

/**
 * Trait DateUtilsTrait
 *
 * @package    Somnambulist\Components\Validation\Rules\Traits
 * @subpackage Somnambulist\Components\Validation\Rules\Traits\DateUtilsTrait
 */
trait DateUtilsTrait
{
    protected function isValidDate(string $date): bool
    {
        return (strtotime($date) !== false);
    }

    protected function getTimeStamp($date): int
    {
        return strtotime($date);
    }
}
