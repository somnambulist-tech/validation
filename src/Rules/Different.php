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
    protected string $message = 'rule.different';
    protected array $fillableParams = ['field'];

    public function check($value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $field        = $this->parameter('field');
        $anotherValue = $this->validation->getValue($field);

        return $value != $anotherValue;
    }
}
