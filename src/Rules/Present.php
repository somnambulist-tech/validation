<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Present
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Present
 */
class Present extends Rule
{
    protected bool $implicit = true;
    protected string $message = 'rule.present';

    public function check($value): bool
    {
        $this->setAttributeAsRequired();

        return $this->validation->hasValue($this->attribute->key());
    }

    protected function setAttributeAsRequired()
    {
        $this->attribute?->makeRequired();
    }
}
