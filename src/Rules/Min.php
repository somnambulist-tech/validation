<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Behaviours\CanObtainSizeValue;

class Min extends Rule
{
    use CanObtainSizeValue;

    protected string $message = 'rule.min';
    protected array $fillableParams = ['min'];

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $min       = $this->getSizeInBytes($this->parameter('min'));
        $valueSize = $this->getValueSize($value);

        if (!is_numeric($valueSize)) {
            return false;
        }

        return $valueSize >= $min;
    }
}
