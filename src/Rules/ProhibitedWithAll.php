<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rules\Behaviours\CanConvertFieldParametersDotNotationToResolvedStrings;

/**
 * Based on Laravel validators required_with_all but here the fields must be missing or empty if
 * all the other specified fields are present and not empty.
 */
class ProhibitedWithAll extends Prohibited
{
    use CanConvertFieldParametersDotNotationToResolvedStrings;

    protected bool $implicit = true;
    protected string $message  = 'rule.prohibited_with_all';

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

        if (!$requiredValidator->check($this->attribute->value())) {
            return true;
        }

        $fieldsHaveValues = true;
        foreach ($fields as $field) {
            $fieldsHaveValues = $fieldsHaveValues && $requiredValidator->check($this->attribute->value($field));
        }

        if (!$fieldsHaveValues) {
            return true;
        }

        return false;
    }
}