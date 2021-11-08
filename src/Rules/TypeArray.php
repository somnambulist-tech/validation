<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class TypeArray
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\TypeArray
 */
class TypeArray extends Rule
{
    protected string $message = "The :attribute must be an array";

    public function check($value): bool
    {
        return is_array($value);
    }
}
