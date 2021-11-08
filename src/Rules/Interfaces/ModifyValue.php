<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Interfaces;

/**
 * Interface ModifyValue
 *
 * @package    Somnambulist\Components\Validation\Rules\Interfaces
 * @subpackage Somnambulist\Components\Validation\Rules\Interfaces\ModifyValue
 */
interface ModifyValue
{
    /**
     * Modify given value so in current and next rules returned value will be used
     */
    public function modifyValue(mixed $value): mixed;
}
