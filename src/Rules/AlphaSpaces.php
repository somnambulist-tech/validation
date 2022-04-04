<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class AlphaSpaces
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\AlphaSpaces
 */
class AlphaSpaces extends Rule
{
    protected string $message = 'rule.alpha_spaces';
    public function check($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[\pL\pM\s]+$/u', $value) > 0;
    }
}
