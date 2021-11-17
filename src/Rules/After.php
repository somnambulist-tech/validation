<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Behaviours\CanValidateDates;

/**
 * Class After
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\After
 */
class After extends Rule
{
    use CanValidateDates;

    protected string $message = "The :attribute must be a date after :time.";
    protected array $fillableParams = ['time'];

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $time = $this->parameter('time');

        $this->assertDate($value);
        $this->assertDate($time);

        return $this->getTimeStamp($time) < $this->getTimeStamp($value);
    }
}
