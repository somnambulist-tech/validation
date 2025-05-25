<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rules\Behaviours\CanConvertFieldParametersDotNotationToResolvedStrings;

/**
 * Based on Laravel validators required_with but here the field must be missing or empty if
 * any of the other specified fields are present and not empty.
 */
class ProhibitedWithout extends Prohibited
{
    use CanConvertFieldParametersDotNotationToResolvedStrings;

    protected string $message  = 'rule.prohibited_without';
    protected bool $implicit = true;

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

        if (!$fieldsHaveValues) {
            return false;
        }

        return $requiredValidator->check($value);
    }
}
