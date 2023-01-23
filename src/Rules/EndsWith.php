<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

use function end;
use function is_array;
use function is_numeric;
use function is_string;
use function strval;

class EndsWith extends Rule
{
    protected string $message = 'rule.ends_with';
    protected array $fillableParams = ['compare_with'];

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $compareWith = $this->parameter('compare_with');

        if (is_string($value) || is_numeric($value)) {
            return str_ends_with(strval($value), $compareWith);
        }

        if (is_array($value)) {
            $lastValue = end($value);

            return $lastValue === $compareWith;
        }

        return false;
    }
}
