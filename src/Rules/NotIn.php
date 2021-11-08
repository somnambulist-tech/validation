<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Helper;
use Somnambulist\Components\Validation\Rule;

/**
 * Class NotIn
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\NotIn
 */
class NotIn extends Rule
{
    protected string $message = "The :attribute does not allow the following values :disallowed_values";
    protected bool $strict = false;

    public function fillParameters(array $params): Rule
    {
        if (count($params) == 1 and is_array($params[0])) {
            $params = $params[0];
        }
        $this->params['disallowed_values'] = $params;

        return $this;
    }

    public function strict(bool $strict = true): self
    {
        $this->strict = $strict;

        return $this;
    }

    public function check($value): bool
    {
        $this->requireParameters(['disallowed_values']);

        $disallowedValues = (array)$this->parameter('disallowed_values');

        $and                  = $this->validation ? $this->validation->getTranslation('and') : 'and';
        $disallowedValuesText = Helper::join(Helper::wraps($disallowedValues, "'"), ', ', ", {$and} ");
        $this->setParameterText('disallowed_values', $disallowedValuesText);

        return !in_array($value, $disallowedValues, $this->strict);
    }
}
