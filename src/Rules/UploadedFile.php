<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Helper;
use Somnambulist\Components\Validation\MimeTypeGuesser;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Interfaces\BeforeValidate;

/**
 * Class UploadedFile
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\UploadedFile
 */
class UploadedFile extends Rule implements BeforeValidate
{
    use Traits\FileTrait, Traits\SizeTrait;

    protected string $message = "The :attribute is not a valid uploaded file";

    public function fillParameters(array $params): Rule
    {
        if (count($params) < 3) {
            return $this;
        }

        $this->minSize(array_shift($params));
        $this->maxSize(array_shift($params));
        $this->fileTypes($params);

        return $this;
    }

    /**
     * Set the minimum filesize
     */
    public function minSize(int|string $size): Rule
    {
        $this->params['min_size'] = $size;

        return $this;
    }

    /**
     * Set the max allowed file size
     */
    public function maxSize(int|string $size): Rule
    {
        $this->params['max_size'] = $size;

        return $this;
    }

    /**
     * Set the array of allowed types e.g. doc|docx|xls|xlsx
     */
    public function fileTypes($types): Rule
    {
        if (is_string($types)) {
            $types = explode('|', $types);
        }

        $this->params['allowed_types'] = $types;

        return $this;
    }

    /**
     * Set the filesize between the min/max
     */
    public function between(int|string $min, int|string $max): Rule
    {
        $this->minSize($min);
        $this->maxSize($max);

        return $this;
    }

    public function beforeValidate(): void
    {
        $attribute = $this->getAttribute();

        // We only resolve uploaded file value
        // from complex attribute such as 'files.photo', 'images.*', 'images.foo.bar', etc.
        if (!$attribute->isUsingDotNotation()) {
            return;
        }

        $keys          = explode(".", $attribute->getKey());
        $firstKey      = array_shift($keys);
        $firstKeyValue = $this->validation->getValue($firstKey);

        $resolvedValue = $this->resolveUploadedFileValue($firstKeyValue);

        // Return original value if $value can't be resolved as uploaded file value
        if (!$resolvedValue) {
            return;
        }

        $this->validation->setValue($firstKey, $resolvedValue);
    }

    public function check($value): bool
    {
        $minSize      = $this->parameter('min_size');
        $maxSize      = $this->parameter('max_size');
        $allowedTypes = $this->parameter('allowed_types');

        if ($allowedTypes) {
            $or = $this->validation ? $this->validation->getTranslation('or') : 'or';
            $this->setParameterText('allowed_types', Helper::join(Helper::wraps($allowedTypes, "'"), ', ', ", {$or} "));
        }

        // below is Required rule job
        if (!$this->isValueFromUploadedFiles($value) or $value['error'] == UPLOAD_ERR_NO_FILE) {
            return true;
        }

        if (!$this->isUploadedFile($value)) {
            return false;
        }

        // just make sure there is no error
        if ($value['error']) {
            return false;
        }

        if ($minSize) {
            $bytesMinSize = $this->getSizeInBytes($minSize);
            if ($value['size'] < $bytesMinSize) {
                $this->setMessage('The :attribute file is too small, minimum size is :min_size');

                return false;
            }
        }

        if ($maxSize) {
            $bytesMaxSize = $this->getSizeInBytes($maxSize);
            if ($value['size'] > $bytesMaxSize) {
                $this->setMessage('The :attribute file is too large, maximum size is :max_size');

                return false;
            }
        }

        if (!empty($allowedTypes)) {
            $guesser = new MimeTypeGuesser;
            $ext     = $guesser->getExtension($value['type']);
            unset($guesser);

            if (!in_array($ext, $allowedTypes)) {
                $this->setMessage('The :attribute file type must be :allowed_types');

                return false;
            }
        }

        return true;
    }
}
