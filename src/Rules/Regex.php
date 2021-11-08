<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Regex
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Regex
 */
class Regex extends Rule
{
    protected string $message = "The :attribute is not a valid format";
    protected array $fillableParams = ['regex'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        return preg_match($this->parameter('regex'), $value) > 0;
    }
}
