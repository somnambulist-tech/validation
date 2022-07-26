<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Contracts;

interface BeforeValidate
{
    public function beforeValidate(): void;
}
