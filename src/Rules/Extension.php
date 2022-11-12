<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Extension extends Rule
{
    protected string $message = 'rule.extension';

    public function fillParameters(array $params): self
    {
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        $this->params['allowed_extensions'] = $params;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['allowed_extensions']);

        $allowedExtensions = $this->parameter('allowed_extensions');

        foreach ($allowedExtensions as $key => $ext) {
            $allowedExtensions[$key] = ltrim($ext, '.');
        }

        $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));

        return $ext && in_array($ext, $allowedExtensions);
    }
}
