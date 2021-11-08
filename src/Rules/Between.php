<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Between
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Between
 */
class Between extends Rule
{
    use Traits\SizeTrait;

    protected string $message = "The :attribute must be between :min and :max";
    protected array $fillableParams = ['min', 'max'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $min = $this->getSizeInBytes($this->parameter('min'));
        $max = $this->getSizeInBytes($this->parameter('max'));

        $valueSize = $this->getValueSize($value);

        if (!is_numeric($valueSize)) {
            return false;
        }

        return ($valueSize >= $min && $valueSize <= $max);
    }
}
