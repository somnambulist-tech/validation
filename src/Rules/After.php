<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

/**
 * Class After
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\After
 */
class After extends Rule
{
    use Traits\DateUtilsTrait;

    protected string $message = "The :attribute must be a date after :time.";
    protected array $fillableParams = ['time'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $time = $this->parameter('time');

        if (!$this->isValidDate($value)) {
            throw ParameterException::invalidDate($value);
        }

        if (!$this->isValidDate($time)) {
            throw ParameterException::invalidDate($time);
        }

        return $this->getTimeStamp($time) < $this->getTimeStamp($value);
    }
}
