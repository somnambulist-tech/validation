<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

use function is_null;
use function preg_match;
use function str_replace;

class Uuid extends Rule
{
    /**
     * Regular expression pattern for matching a UUID of any variant.
     *
     * Taken from Ramsey\Uuid\Validator\GenericValidator
     */
    private const VALID_PATTERN = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z';
    private const NIL = '00000000-0000-0000-0000-000000000000';

    protected string $message = 'rule.uuid';
    protected bool $implicit = true;

    public function check(mixed $value): bool
    {
        return !is_null($value) && $this->validate($value) && $value !== self::NIL;
    }

    private function validate(string $uuid): bool
    {
        $uuid = str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}'], '', $uuid);

        return $uuid === self::NIL || preg_match('/' . self::VALID_PATTERN . '/Dms', $uuid);
    }
}
