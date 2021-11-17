<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class TypeStringRule
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\TypeStringRule
 */
class TypeString extends Rule
{
    protected string $message = ':attribute must be a string';

    public function check($value): bool
    {
        return is_string($value);
    }
}
