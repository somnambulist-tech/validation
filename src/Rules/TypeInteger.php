<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class TypeInteger
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\TypeInteger
 */
class TypeInteger extends Rule
{
    protected string $message = 'rule.integer';
    public function check($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
