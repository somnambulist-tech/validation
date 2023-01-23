<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

use function is_numeric;
use function is_string;
use function reset;
use function str_starts_with;
use function strval;

class StartsWith extends Rule
{
    protected string $message = 'rule.starts_with';
    protected array $fillableParams = ['compare_with'];

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $compareWith = $this->parameter('compare_with');

        if (is_string($value) || is_numeric($value)) {
            return str_starts_with(strval($value), $compareWith);
        }

        if (is_array($value)) {
            $firstValue = reset($value);

            return $firstValue === $compareWith;
        }

        return false;
    }
}
