<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules\Behaviours;

use RuntimeException;
use Somnambulist\Components\Validation\Helper;

use function is_uploaded_file;

trait CanValidateFiles
{
    public function isUploadedFile(mixed $value): bool
    {
        if (!$this->isValueFromUploadedFiles($value)) {
            return false;
        }
        if (is_array($value['tmp_name'])) {
            $attr = $this->attribute()?->key() ?? 'files';

            throw new RuntimeException(sprintf('Attribute "%s" has multiple files, use "%s.*" as the attribute key', $attr, $attr));
        }

        return is_uploaded_file($value['tmp_name']);
    }

    protected function isValueFromUploadedFiles(mixed $value): bool
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

    protected function resolveUploadedFileValue(mixed $value): ?array
    {
        if (!$this->isValueFromUploadedFiles($value)) {
            return null;
        }

        // Here $value should be an array:
        // [
        //      'name'      => string|array,
        //      'type'      => string|array,
        //      'size'      => int|array,
        //      'tmp_name'  => string|array,
        //      'error'     => string|array,
        // ]

        // Flatten $value to an array with dot formatted keys,
        // so our array must be something like:
        // ['name' => string, 'type' => string, 'size' => int, ...]
        // or for multiple values:
        // ['name.0' => string, 'name.1' => string, 'type.0' => string, 'type.1' => string, ...]
        // or for nested array:
        // ['name.foo.bar' => string, 'name.foo.baz' => string, 'type.foo.bar' => string, 'type.foo.baz' => string, ...]
        $arrayDots = Helper::arrayDot($value);

        $results = [];

        foreach ($arrayDots as $key => $val) {
            // Move first key to last key
            // name.foo.bar -> foo.bar.name
            $splits   = explode('.', $key);
            $firstKey = array_shift($splits);
            $key      = count($splits) ? implode(".", $splits) . '.' . $firstKey : $firstKey;

            Helper::arraySet($results, $key, $val);
        }

        return $results;
    }
}
