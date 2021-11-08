<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Interfaces\ModifyValue;

/**
 * Class Defaults
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Defaults
 */
class Defaults extends Rule implements ModifyValue
{
    protected string $message = "The :attribute default is :default";
    protected array $fillableParams = ['default'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        return true;
    }

    public function modifyValue($value): mixed
    {
        return $this->isEmptyValue($value) ? $this->parameter('default') : $value;
    }

    protected function isEmptyValue($value): bool
    {
        return false === (new Required)->check($value);
    }
}
