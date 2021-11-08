<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

/**
 * Class Before
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Before
 */
class Before extends Rule
{
    use Traits\DateUtilsTrait;

    protected string $message = "The :attribute must be a date before :time.";
    protected array $fillableParams = ['time'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $time = $this->parameter('time');

        if (!$this->isValidDate($value)) {
            throw ParameterException::invalidDate($value);
        }

        if (!$this->isValidDate($time)) {
            throw ParameterException::invalidDate($value);
        }

        return $this->getTimeStamp($time) > $this->getTimeStamp($value);
    }
}
