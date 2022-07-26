<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Contracts;

interface ModifyValue
{
    /**
     * Modify given value so in current and next rules returned value will be used
     */
    public function modifyValue(mixed $value): mixed;
}
