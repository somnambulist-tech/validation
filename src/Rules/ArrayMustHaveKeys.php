<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Helper;
use Somnambulist\Components\Validation\Rule;

use function array_key_exists;
use function count;
use function is_array;
use function sprintf;

class ArrayMustHaveKeys extends Rule
{
    protected string $message = 'rule.array_must_have_keys';
    protected array $fillableParams = ['keys'];

    public static function make(array $values): string
    {
        return sprintf('array_must_have_keys:%s', Helper::flattenValues($values));
    }

    public function fillParameters(array $params): self
    {
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }

        $this->params['keys'] = $params;

        return $this;
    }

    public function keys(array $keys): self
    {
        $this->params['keys'] = $keys;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['keys']);

        if (!is_array($value)) {
            return false;
        }

        $ret = true;

        foreach ($this->parameter('keys') as $key) {
            $ret = $ret && array_key_exists($key, $value);
        }

        return $ret;
    }
}
