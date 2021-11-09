<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

/**
 * Class RequiredWithAll
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\RequiredWithAll
 */
class RequiredWithAll extends Required
{
    protected bool $implicit = true;
    protected string $message = "The :attribute is required";

    public function fillParameters(array $params): self
    {
        $this->params['fields'] = $params;

        return $this;
    }

    public function check($value): bool
    {
        $this->requireParameters(['fields']);

        $fields            = $this->parameter('fields');
        $validator         = $this->validation->getFactory();
        $requiredValidator = $validator('required');

        foreach ($fields as $field) {
            if (!$this->validation->hasValue($field)) {
                return true;
            }
        }

        $this->setAttributeAsRequired();

        return $requiredValidator->check($value);
    }
}
