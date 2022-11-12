<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class Date extends Rule
{
    protected string $message = 'rule.date';
    protected array $fillableParams = ['format'];
    protected array $params = [
        'format' => 'Y-m-d',
    ];

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $format = $this->parameter('format');

        return date_create_from_format($format, (string)$value) !== false;
    }
}
