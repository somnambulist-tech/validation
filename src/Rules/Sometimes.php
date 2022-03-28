<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

/**
 * Class Sometimes
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Sometimes
 */
class Sometimes extends Required
{
    public function check($value): bool
    {
        return true;
    }
}
