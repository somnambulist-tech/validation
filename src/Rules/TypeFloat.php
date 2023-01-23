<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

use function gettype;

class TypeFloat extends Rule
{
    protected string $message = 'rule.float';

    public function check(mixed $value): bool
    {
        // https://www.php.net/manual/en/function.is-float.php#117304
        if (!is_scalar($value)) {
            return false;
        }

        if ('double' === gettype($value)) {
            return true;
        } else {
            return preg_match('/^\\d+\\.\\d+$/', (string)$value) === 1;
        }
    }
}
