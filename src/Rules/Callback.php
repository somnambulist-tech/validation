<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Closure;
use InvalidArgumentException;
use Somnambulist\Components\Validation\Rule;

class Callback extends Rule
{
    protected string $message = 'rule.default';
    protected array $fillableParams = ['callback'];

    public function through(Closure $callback): self
    {
        $this->params['callback'] = $callback;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters($this->fillableParams);

        $callback = $this->parameter('callback');

        if (!$callback instanceof Closure) {
            throw new InvalidArgumentException(sprintf('Callback rule for "%s" is not callable.', $this->attribute->key()));
        }

        $callback       = $callback->bindTo($this, $this);
        $invalidMessage = $callback($value);

        if (is_string($invalidMessage)) {
            $this->message = $invalidMessage;

            return false;
        }

        return $invalidMessage;
    }
}
