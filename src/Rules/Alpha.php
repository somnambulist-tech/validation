<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Alpha
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Alpha
 */
class Alpha extends Rule
{
    protected string $message = 'rule.alpha';
    public function check($value): bool
    {
        return is_string($value) && preg_match('/^[\pL\pM]+$/u', $value);
    }
}
