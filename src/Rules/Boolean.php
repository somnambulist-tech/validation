<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;
use function in_array;

/**
 * Class Boolean
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Boolean
 */
class Boolean extends Rule
{
    protected string $message = "The :attribute must be a boolean";

    public function check($value): bool
    {
        return in_array($value, [true, false, "true", "false", 1, 0, "0", "1", "y", "n"], true);
    }
}
