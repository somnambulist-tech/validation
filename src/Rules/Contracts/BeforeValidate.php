<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Contracts;

/**
 * Interface BeforeValidate
 *
 * @package    Somnambulist\Components\Validation\Rules\Contracts
 * @subpackage Somnambulist\Components\Validation\Rules\Contracts\BeforeValidate
 */
interface BeforeValidate
{
    public function beforeValidate(): void;
}
