<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Digits
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Digits
 */
class Digits extends Rule
{
    protected string $message = 'rule.digits';
    protected array $fillableParams = ['length'];

    public function check($value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $length = (int)$this->parameter('length');

        return !preg_match('/[^0-9]/', (string)$value) && strlen((string)$value) == $length;
    }
}
