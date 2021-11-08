<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Numeric
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Numeric
 */
class Numeric extends Rule
{
    protected string $message = "The :attribute must be numeric";

    public function check($value): bool
    {
        return is_numeric($value);
    }
}
