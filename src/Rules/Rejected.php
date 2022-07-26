<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Rejected extends Rule
{
    protected bool $implicit = true;
    protected string $message = 'rule.rejected';
    protected array $params = ['rejected' => ['no', 'off', '0', 0, false, 'false']];

    public function check(mixed $value): bool
    {
        return in_array($value, $this->params['rejected'], true);
    }
}
