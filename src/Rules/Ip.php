<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Ip
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Ip
 */
class Ip extends Rule
{
    protected string $message = 'rule.ip';
    public function check($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}
