<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Min
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Min
 */
class Min extends Rule
{
    use Traits\SizeTrait;

    protected string $message = "The :attribute minimum is :min";
    protected array $fillableParams = ['min'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $min       = $this->getSizeInBytes($this->parameter('min'));
        $valueSize = $this->getValueSize($value);

        if (!is_numeric($valueSize)) {
            return false;
        }

        return $valueSize >= $min;
    }
}
