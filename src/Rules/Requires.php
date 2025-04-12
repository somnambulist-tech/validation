<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rules\Behaviours\CanConvertFieldParametersDotNotationToResolvedStrings;
use function str_replace;

class Requires extends Required
{
    use CanConvertFieldParametersDotNotationToResolvedStrings;

    protected bool $implicit = true;
    protected string $message = 'rule.requires';

    public function fillParameters(array $params): self
    {
        $this->params['fields'] = $params;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['fields']);

        $fields = $this->parameter('fields');

        foreach ($fields as $field) {
            if (!$this->attribute->value($field)) {
                return false;
            }
        }

        return true;
    }
}
