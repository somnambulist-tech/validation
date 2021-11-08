<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class DigitsBetween
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\DigitsBetween
 */
class DigitsBetween extends Rule
{
    protected string $message = "The :attribute must have a length between :min and :max";
    protected array $fillableParams = ['min', 'max'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $min = (int)$this->parameter('min');
        $max = (int)$this->parameter('max');

        $length = strlen((string)$value);

        return !preg_match('/[^0-9]/', (string)$value) && $length >= $min && $length <= $max;
    }
}
