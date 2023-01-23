<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use function array_map;
use function function_exists;
use function implode;
use function is_array;
use function is_callable;
use function str_replace;

class Helper
{
    /**
     * Check if an item or items exist in an array using "dot" notation.
     * Adapted from: https://github.com/illuminate/support/blob/v5.3.23/Arr.php#L81
     *
     * @param array  $array
     * @param string $key
     *
     * @return bool
     */
    public static function arrayHas(array $array, string $key): bool
    {
        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get an item from an array using "dot" notation.
     * Adapted from: https://github.com/illuminate/support/blob/v5.3.23/Arr.php#L246
     *
     * @param array       $array
     * @param string|null $key
     * @param mixed       $default
     *
     * @return mixed
     */
    public static function arrayGet(array $array, mixed $key, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return is_callable($default) ? $default() : $default;
            }
        }

        return $array;
    }

    /**
     * Flatten a multidimensional associative array with dots.
     * Adapted from: https://github.com/illuminate/support/blob/v5.3.23/Arr.php#L81
     *
     * @param array  $array
     * @param string $prepend
     *
     * @return array
     */
    public static function arrayDot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, static::arrayDot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * Set an item on an array or object using dot notation.
     * Adapted from: https://github.com/illuminate/support/blob/v5.3.23/helpers.php#L437
     *
     * @param mixed             $target
     * @param string|array|null $key
     * @param mixed             $value
     * @param bool              $overwrite
     *
     * @return mixed
     */
    public static function arraySet(mixed &$target, mixed $key, mixed $value, bool $overwrite = true): array
    {
        if (is_null($key)) {
            if ($overwrite) {
                return $target = array_merge($target, $value);
            }

            return $target = array_merge($value, $target);
        }

        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (!is_array($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    static::arraySet($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (is_array($target)) {
            if ($segments) {
                if (!array_key_exists($segment, $target)) {
                    $target[$segment] = [];
                }

                static::arraySet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !array_key_exists($segment, $target)) {
                $target[$segment] = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                static::arraySet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }

    /**
     * Unset an item on an array or object using dot notation.
     *
     * @param mixed        $target
     * @param string|array $key
     *
     * @return mixed
     */
    public static function arrayUnset(mixed &$target, string|array $key): mixed
    {
        if (!is_array($target)) {
            return $target;
        }

        $segments = is_array($key) ? $key : explode('.', $key);
        $segment  = array_shift($segments);

        if ($segment == '*') {
            $target = [];
        } elseif ($segments) {
            if (array_key_exists($segment, $target)) {
                static::arrayUnset($target[$segment], $segments);
            }
        } elseif (array_key_exists($segment, $target)) {
            unset($target[$segment]);
        }

        return $target;
    }

    /**
     * Returns a string of comma separated values
     *
     * @param array $values
     *
     * @return string
     */
    public static function flattenValues(array $values): string
    {
        return implode(',', array_map(static fn($value) => '"' . str_replace('"', '""', (string)$value) . '"', $values));
    }

    /**
     * Join string[] to string with given $separator and $lastSeparator.
     *
     * @param array       $pieces
     * @param string      $separator
     * @param string|null $lastSeparator
     *
     * @return string
     */
    public static function join(array $pieces, string $separator, string $lastSeparator = null): string
    {
        if (is_null($lastSeparator)) {
            $lastSeparator = $separator;
        }

        $last = array_pop($pieces);

        return match (count($pieces)) {
            0 => (string)$last ?: '',
            1 => $pieces[0] . $lastSeparator . $last,
            default => implode($separator, $pieces) . $lastSeparator . $last,
        };
    }

    /**
     * Wrap string[] by given $prefix and $suffix
     *
     * @param array       $strings
     * @param string      $prefix
     * @param string|null $suffix
     *
     * @return array
     */
    public static function wraps(array $strings, string $prefix, string $suffix = null): array
    {
        if (is_null($suffix)) {
            $suffix = $prefix;
        }

        return array_map(function ($str) use ($prefix, $suffix) {
            return $prefix . $str . $suffix;
        }, $strings);
    }

    /**
     * Returns true if the array is not a list (helper to handle PHP 8.1 compatibility)
     *
     * @param array $array
     *
     * @return bool
     */
    public static function arrayIsList(array $array): bool
    {
        if (!function_exists('array_is_list')) {
            $i = 0;
            foreach ($array as $k => $v) {
                if ($k !== $i++) {
                    return false;
                }
            }

            return true;
        }

        return array_is_list($array);
    }

    /**
     * Returns true if the array is not a list (helper to handle PHP 8.1 compatibility)
     *
     * @param array $array
     *
     * @return bool
     */
    public static function arrayIsNested(array $array): bool
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                return true;
            }
        }

        return false;
    }
}
