<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Different extends Rule
{
    protected string $message = 'rule.different';
    protected array $fillableParams = ['field'];

    public function field(string $field): self
    {
        $this->params['field'] = $field;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $field        = $this->parameter('field');
        $anotherValue = $this->validation->input()->get($field);

        return $value != $anotherValue;
    }
}
