<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Behaviours;

use Somnambulist\Components\Validation\Exceptions\ParameterException;

trait CanValidateDates
{
    protected function assertDate(int|string $date): void
    {
        if ($this->getTimeStamp($date) === null) {
            throw ParameterException::invalidDate($date);
        }
    }

    protected function getTimeStamp(int|string $date): ?int
    {
        if (is_int($date)) {
            return $date;
        }

        if (is_string($date) && is_numeric($date)) {
            return (int)$date;
        }

        $timestamp = strtotime($date);
        return $timestamp !== false ? $timestamp : null;
    }
}
