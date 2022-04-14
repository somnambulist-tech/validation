<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Behaviours;

use function array_map;

/**
 * Trait CanConvertValuesToBooleans
 *
 * @package    Somnambulist\Components\Validation\Rules\Behaviours
 * @subpackage Somnambulist\Components\Validation\Rules\Behaviours\CanConvertValuesToBooleans
 */
trait CanConvertValuesToBooleans
{
    protected function convertStringsToBoolean(array $values): array
    {
        return array_map(function ($value) {

            if ($value === 'true') {
                return true;
            } elseif ($value === 'false') {
                return false;
            }

            return $value;
        }, $values);
    }

    protected function convertBooleansToString(array $values): array
    {
        return array_map(function ($value) {

            if ($value === true) {
                return 'true';
            } elseif ($value === false) {
                return 'false';
            }

            return (string)$value;
        }, $values);
    }
}
