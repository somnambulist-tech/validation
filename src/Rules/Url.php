<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Url extends Rule
{
    protected string $message = 'rule.url';

    public function fillParameters(array $params): self
    {
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }

        return $this->forScheme(...$params);
    }

    public function forScheme(string ...$scheme): self
    {
        $this->params['schemes'] = $scheme;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $schemes = $this->parameter('schemes');

        if (!$schemes) {
            return $this->validateCommonScheme((string)$value);
        } else {
            foreach ($schemes as $scheme) {
                if ($this->validateCommonScheme((string)$value, $scheme)) {
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
        if ($scheme) {
            return $this->validateBasic($value) && preg_match("/^$scheme:\/\//", $value);
        }

        return $this->validateBasic($value) && preg_match("/^\w+:\/\//i", $value);
    }

    /**
     * Validate $value conforms to standard URL rules according to PHP filter_validate_url
     */
    private function validateBasic(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}
