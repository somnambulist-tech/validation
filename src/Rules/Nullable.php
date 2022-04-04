<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Nullable
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Nullable
 */
class Nullable extends Rule
{
    public function check($value): bool
    {
        return true;
    }
}
