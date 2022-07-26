<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Behaviours;

use InvalidArgumentException;

trait CanObtainSizeValue
{
    /**
     * Get size (int) value from given $value
     */
    protected function getValueSize(mixed $value): float
    {
        if ($this->attribute()
            && ($this->attribute()->rules()->hasAnyOf('numeric', 'integer'))
            && is_numeric($value)
        ) {
            $value = (float)$value;
        }

        if (is_int($value) || is_float($value)) {
            return (float)$value;
        } elseif (is_string($value)) {
            return (float)mb_strlen($value, 'UTF-8');
        } elseif ($this->isUploadedFileValue($value)) {
            return (float)$value['size'];
        } elseif (is_array($value)) {
            return (float)count($value);
        } else {
            return 0.0;
        }
    }

    /**
     * Check whether value is from $_FILES
     */
    public function isUploadedFileValue(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $keys = ['name', 'type', 'tmp_name', 'size', 'error'];

        foreach ($keys as $key) {
            if (!array_key_exists($key, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function getSizeInBytes(int|float|string $size): float
    {
        if (is_numeric($size)) {
            return (float)$size;
        }

        if (!is_string($size)) {
            throw new InvalidArgumentException("Size must be string or numeric bytes");
        }

        if (!preg_match("/^(?<number>((\d+)?\.)?\d+)(?<format>([BKMGTP])B?)?$/i", $size, $match)) {
            throw new InvalidArgumentException("Size is not valid format, expected number + B, KB, MB, GB, TB, PB");
        }

        $number = (float)$match['number'];
        $format = $match['format'] ?? '';

        return match (strtoupper($format)) {
            "KB", "K" => $number * 1024,
            "MB", "M" => $number * pow(1024, 2),
            "GB", "G" => $number * pow(1024, 3),
            "TB", "T" => $number * pow(1024, 4),
            "PB", "P" => $number * pow(1024, 5),
            default => $number,
        };
    }
}
