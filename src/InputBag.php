<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use function array_merge;

/**
 * Class InputBag
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\InputBag
 */
class InputBag implements Countable, IteratorAggregate
{
    public function __construct(private array $data = [])
    {
    }

    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    public function count()
    {
        return count($this->data);
    }

    public function get(?string $key, mixed $default = null): mixed
    {
        return Helper::arrayGet($this->data, $key, $default);
    }

    public function merge(array $params): self
    {
        $this->data = array_merge($this->data, $params);

        return $this;
    }

    public function only(string ...$key): array
    {
        $ret = [];

        foreach ($key as $k) {
            $ret[$k] = $this->get($k);
        }

        return $ret;
    }

    public function set(string $key, mixed $value): self
    {
        Helper::arraySet($this->data, $key, $value);

        return $this;
    }

    public function has(string $key): bool
    {
        return Helper::arrayHas($this->data, $key);
    }
}
