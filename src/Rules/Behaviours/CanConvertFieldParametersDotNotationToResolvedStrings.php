<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Behaviours;

trait CanConvertFieldParametersDotNotationToResolvedStrings
{
    protected function convertParametersForMessage(): array
    {
        $ret = $this->params;

        if ($this->attribute->isArrayAttribute()) {
            foreach ($ret['fields'] as $key => $field) {
                $ret['fields'][$key] = $this->attribute->siblingField($field);
            }
        }

        return $ret;
    }
}
