<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Behaviours;

use Somnambulist\Components\Validation\Exceptions\ParameterException;

trait CanValidateDates
{
    protected function assertDate(string $date): void
    {
        if (!$this->isValidDate($date)) {
            throw ParameterException::invalidDate($date);
        }
    }

    protected function isValidDate(string $date): bool
    {
        return (strtotime($date) !== false);
    }

    protected function getTimeStamp($date): int
    {
        return strtotime($date);
    }
}
