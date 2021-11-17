<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Helper;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Behaviours\CanConvertValuesToBooleans;
use function array_shift;
use function in_array;
use function is_bool;

/**
 * Class ProhibitedUnlessRule
 *
 * Based on Laravel validators prohibited_unless
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\ProhibitedUnlessRule
 */
class ProhibitedUnless extends Rule
{
    use CanConvertValuesToBooleans;

    protected string $message  = ':attribute is not allowed if :field does not have value(s) :values';
    protected bool $implicit = true;

    public function fillParameters(array $params): Rule
    {
        $this->params['field']  = array_shift($params);
        $this->params['values'] = $this->convertStringsToBoolean($params);

        return $this;
    }

    public function check($value): bool
    {
        $this->requireParameters(['field', 'values']);

        $anotherAttribute = $this->parameter('field');
        $definedValues    = $this->parameter('values');
        $anotherValue     = $this->getAttribute()->getValue($anotherAttribute);

        if ($definedValues) {
            $or = $this->validation ? $this->validation->getTranslation('or') : 'or';
            $this->setParameterText('values', Helper::join(Helper::wraps($this->convertBooleansToString($definedValues), "'"), ', ', ", {$or} "));
        }

        $requiredValidator = $this->validation->getFactory()->getRule('required');

        if (!in_array($anotherValue, $definedValues, is_bool($anotherValue))) {
            return !$requiredValidator->check($value);
        }

        return true;
    }
}
