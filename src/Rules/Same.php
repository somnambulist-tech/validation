<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Same
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Same
 */
class Same extends Rule
{
    protected string $message = "The :attribute must be same with :field";
    protected array $fillableParams = ['field'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $field        = $this->parameter('field');
        $anotherValue = $this->getAttribute()->getValue($field);

        return $value == $anotherValue;
    }
}
