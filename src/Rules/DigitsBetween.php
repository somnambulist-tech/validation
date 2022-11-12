<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class DigitsBetween extends Rule
{
    protected string $message = 'rule.digits_between';
    protected array $fillableParams = ['min', 'max'];

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $min = (int)$this->parameter('min');
        $max = (int)$this->parameter('max');

        $length = strlen((string)$value);

        return !preg_match('/[^0-9]/', (string)$value) && $length >= $min && $length <= $max;
    }
}
