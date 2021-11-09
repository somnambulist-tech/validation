<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

/**
 * Class RequiredUnless
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\RequiredUnless
 */
class RequiredUnless extends Required
{
    protected bool $implicit = true;
    protected string $message = "The :attribute is required";

    public function fillParameters(array $params): self
    {
        $this->params['field']  = array_shift($params);
        $this->params['values'] = $params;

        return $this;
    }

    public function check($value): bool
    {
        $this->requireParameters(['field', 'values']);

        $anotherAttribute = $this->parameter('field');
        $definedValues    = $this->parameter('values');
        $anotherValue     = $this->getAttribute()->getValue($anotherAttribute);

        $validator         = $this->validation->getFactory();
        $requiredValidator = $validator('required');

        if (!in_array($anotherValue, $definedValues)) {
            $this->setAttributeAsRequired();

            return $requiredValidator->check($value);
        }

        return true;
    }
}
