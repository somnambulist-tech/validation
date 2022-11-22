<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Regex extends Rule
{
    protected string $message = 'rule.regex';
    protected array $fillableParams = ['regex'];

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        return preg_match($this->parameter('regex'), (string)$value) > 0;
    }
}
