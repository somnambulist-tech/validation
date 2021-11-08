<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Different
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Different
 */
class Different extends Rule
{
    protected string $message = "The :attribute must be different to :field";
    protected array $fillableParams = ['field'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $field        = $this->parameter('field');
        $anotherValue = $this->validation->getValue($field);

        return $value != $anotherValue;
    }
}
