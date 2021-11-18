<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Helper;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Behaviours\CanConvertValuesToBooleans;
use function array_shift;
use function in_array;
use function is_bool;

/**
 * Class ProhibitedIfRule
 *
 * Based on Laravel validators prohibited_if
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\ProhibitedIfRule
 */
class ProhibitedIf extends Rule
{
    use CanConvertValuesToBooleans;

    protected string $message  = 'rule.prohibited_if';
    protected bool $implicit = true;

    public function fillParameters(array $params): Rule
    {
        $this->params['field']  = array_shift($params);
        $this->params['values'] = $this->convertStringsToBoolean($params);

        return $this;
    }

    public function check($value): bool
    {
        $this->assertHasRequiredParameters(['field', 'values']);

        $anotherAttribute = $this->parameter('field');
        $definedValues    = $this->parameter('values');
        $anotherValue     = $this->attribute()->value($anotherAttribute);

        $requiredValidator = $this->validation->getFactory()->rule('required');

        if (in_array($anotherValue, $definedValues, is_bool($anotherValue))) {
            return !$requiredValidator->check($value);
        }

        return true;
    }

    protected function convertParametersForMessage(): array
    {
        return array_merge($this->params, ['values' => $this->convertBooleansToString($this->params['values'])]);
    }
}
