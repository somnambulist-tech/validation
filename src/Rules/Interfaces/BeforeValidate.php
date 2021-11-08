<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Interfaces;

/**
 * Interface BeforeValidate
 *
 * @package    Somnambulist\Components\Validation\Rules\Interfaces
 * @subpackage Somnambulist\Components\Validation\Rules\Interfaces\BeforeValidate
 */
interface BeforeValidate
{
    public function beforeValidate(): void;
}
