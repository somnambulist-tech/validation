<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Accepted extends Rule
{
    protected bool $implicit = true;
    protected string $message = 'rule.accepted';
    protected array $params = ['accepted' => ['yes', 'on', '1', 1, true, 'true']];

    public function check(mixed $value): bool
    {
        return in_array($value, $this->params['accepted'], true);
    }
}
