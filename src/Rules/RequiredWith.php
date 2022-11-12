<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

class RequiredWith extends Required
{
    protected bool $implicit = true;
    protected string $message = 'rule.required_with';

    public function fillParameters(array $params): self
    {
        $this->params['fields'] = $params;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['fields']);

        $fields            = $this->parameter('fields');
        $requiredValidator = $this->validation->factory()->rule('required');

        foreach ($fields as $field) {
            if ($this->validation->input()->has($field)) {
                $this->setAttributeAsRequired();

                return $requiredValidator->check($value);
            }
        }

        return true;
    }
}
