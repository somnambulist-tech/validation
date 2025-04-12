<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rules\Behaviours\CanConvertFieldParametersDotNotationToResolvedStrings;

class RequiredWithAll extends Required
{
    use CanConvertFieldParametersDotNotationToResolvedStrings;

    protected bool $implicit = true;
    protected string $message = 'rule.required_with_all';

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
            if (!$this->attribute->value($field)) {
                return true;
            }
        }

        $this->setAttributeAsRequired();

        return $requiredValidator->check($value);
    }
}
