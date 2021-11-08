<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Max
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Max
 */
class Max extends Rule
{
    use Traits\SizeTrait;

    protected string $message = "The :attribute maximum is :max";
    protected array $fillableParams = ['max'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $max       = $this->getSizeInBytes($this->parameter('max'));
        $valueSize = $this->getValueSize($value);

        if (!is_numeric($valueSize)) {
            return false;
        }

        return $valueSize <= $max;
    }
}
