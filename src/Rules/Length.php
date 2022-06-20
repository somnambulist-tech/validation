<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Behaviours\CanObtainSizeValue;

/**
 * Class Length
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Length
 */
class Length extends Rule
{
    protected string $message = 'rule.length';
    protected array $fillableParams = ['length'];

    public function check($value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        return (int)$this->parameter('length') === mb_strlen((string)$value);
    }
}
