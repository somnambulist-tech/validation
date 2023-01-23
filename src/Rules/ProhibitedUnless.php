<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Behaviours\CanConvertValuesToBooleans;

use function array_merge;
use function array_shift;
use function in_array;
use function is_bool;

/**
 * Based on Laravel validators prohibited_unless
 */
class ProhibitedUnless extends Rule
{
    use CanConvertValuesToBooleans;

    protected string $message  = 'rule.prohibited_unless';
    protected bool $implicit = true;

    public function fillParameters(array $params): Rule
    {
        $this->params['field']  = array_shift($params);
        $this->params['values'] = $this->convertStringsToBoolean($params);

        return $this;
    }

    public function field(string $field): self
    {
        $this->params['field'] = $field;

        return $this;
    }

    public function values(array $values): self
    {
        $this->params['values'] = $values;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['field', 'values']);

        $anotherAttribute = $this->parameter('field');
        $definedValues    = $this->parameter('values');
        $anotherValue     = $this->attribute()->value($anotherAttribute);

        $requiredValidator = $this->validation->factory()->rule('required');

        if (!in_array($anotherValue, $definedValues, is_bool($anotherValue))) {
            return !$requiredValidator->check($value);
        }

        return true;
    }

    protected function convertParametersForMessage(): array
    {
        return array_merge($this->params, ['values' => $this->convertBooleansToString($this->params['values'])]);
    }
}
