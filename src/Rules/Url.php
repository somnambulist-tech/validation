<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Url
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Url
 */
class Url extends Rule
{
    protected string $message = 'The :attribute is not a valid URL';

    public function fillParameters(array $params): Rule
    {
        if (count($params) == 1 and is_array($params[0])) {
            $params = $params[0];
        }

        return $this->forScheme(...$params);
    }

    public function forScheme(string ...$scheme): Rule
    {
        $this->params['schemes'] = (array)$scheme;

        return $this;
    }

    public function check($value): bool
    {
        $schemes = $this->parameter('schemes');

        if (!$schemes) {
            return $this->validateCommonScheme($value);
        } else {
            foreach ($schemes as $scheme) {
                if ($this->validateCommonScheme($value, $scheme)) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Validate $value has a scheme or has the specified scheme
     */
    private function validateCommonScheme(string $value, string $scheme = null): bool
    {
        if (!$scheme) {
            return $this->validateBasic($value) && (bool)preg_match("/^\w+:\/\//i", $value);
        } else {
            return $this->validateBasic($value) && (bool)preg_match("/^{$scheme}:\/\//", $value);
        }
    }

    /**
     * Validate $value conforms to standard URL rules according to PHP filter_validate_url
     */
    private function validateBasic(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}
