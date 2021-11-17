<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class ProhibitedRule
 *
 * Based on Laravel validators prohibited
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\ProhibitedRule
 */
class Prohibited extends Rule
{
    protected string $message = ':attribute is not allowed';

    public function check($value): bool
    {
        return false;
    }
}
