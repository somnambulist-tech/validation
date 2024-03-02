<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Present extends Rule
{
    protected bool $implicit = true;
    protected string $message = 'rule.present';

    public function check(mixed $value): bool
    {
        $this->setAttributeAsRequired();

        return $this->validation->input()->has($this->attribute->key());
    }

    protected function setAttributeAsRequired(): void
    {
        $this->attribute?->makeRequired();
    }
}
