<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Closure;
use InvalidArgumentException;
use Somnambulist\Components\Validation\Rule;

/**
 * Class Callback
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Callback
 */
class Callback extends Rule
{
    protected string $message = "The :attribute is not valid";
    protected array $fillableParams = ['callback'];

    public function setCallback(Closure $callback): Rule
    {
        return $this->setParameter('callback', $callback);
    }

    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $callback = $this->parameter('callback');

        if (!$callback instanceof Closure) {
            throw new InvalidArgumentException(sprintf('Callback rule for "%s" is not callable.', $this->attribute->getKey()));
        }

        $callback       = $callback->bindTo($this);
        $invalidMessage = $callback($value);

        if (is_string($invalidMessage)) {
            $this->setMessage($invalidMessage);

            return false;
        }

        return $invalidMessage;
    }
}
