<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rules\Behaviours\CanConvertFieldParametersDotNotationToResolvedStrings;

class RequiredWithoutAll extends Required
{
    use CanConvertFieldParametersDotNotationToResolvedStrings;

    protected bool $implicit = true;
    protected string $message = 'rule.required_without_all';

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

        $fieldsHaveValues = true;
        foreach ($fields as $field) {
            $fieldsHaveValues = $fieldsHaveValues && $requiredValidator->check($this->attribute->value($field));
        }

        if ($fieldsHaveValues) {
            return true;
        }

        $this->setAttributeAsRequired();

        return $requiredValidator->check($value);
    }
}
