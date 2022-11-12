<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Contracts\MimeTypeGuesser as MimeTypeGuesserContract;
use Somnambulist\Components\Validation\MimeTypeGuesser;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Behaviours\CanObtainSizeValue;
use Somnambulist\Components\Validation\Rules\Behaviours\CanValidateFiles;
use Somnambulist\Components\Validation\Rules\Contracts\BeforeValidate;

class UploadedFile extends Rule implements BeforeValidate
{
    use CanValidateFiles;
    use CanObtainSizeValue;

    protected string $message = 'rule.uploaded_file';
    protected MimeTypeGuesserContract $guesser;

    public function __construct(MimeTypeGuesserContract $guesser = null)
    {
        $this->guesser = $guesser ?? new MimeTypeGuesser();
    }

    public function fillParameters(array $params): self
    {
        if (count($params) < 2) {
            return $this;
        }

        $this->minSize(array_shift($params));
        $this->maxSize(array_shift($params));
        $this->types($params);

        return $this;
    }

    /**
     * Set the minimum filesize
     */
    public function minSize(int|string $size): self
    {
        $this->params['min_size'] = $size;

        return $this;
    }

    /**
     * Set the max allowed file size
     */
    public function maxSize(int|string $size): self
    {
        $this->params['max_size'] = $size;

        return $this;
    }

    /**
     * Set the filesize between the min/max
     */
    public function between(int|string $min, int|string $max): self
    {
        $this->minSize($min);
        $this->maxSize($max);

        return $this;
    }

    /**
     * Set the array of allowed types e.g. doc,docx,xls,xlsx
     */
    public function types($types): self
    {
        if (is_string($types)) {
            $types = explode(',', $types);
        }

        $this->params['allowed_types'] = $types;

        return $this;
    }

    public function beforeValidate(): void
    {
        $attribute = $this->attribute();

        // We only resolve uploaded file value
        // from complex attribute such as 'files.photo', 'images.*', 'images.foo.bar', etc.
        if (!$attribute->isUsingDotNotation()) {
            return;
        }

        $keys          = explode(".", $attribute->key());
        $firstKey      = array_shift($keys);
        $firstKeyValue = $this->validation->input()->get($firstKey);

        $resolvedValue = $this->resolveUploadedFileValue($firstKeyValue);

        // Return original value if $value can't be resolved as uploaded file value
        if (!$resolvedValue) {
            return;
        }

        $this->validation->input()->set($firstKey, $resolvedValue);
    }

    public function check(mixed $value): bool
    {
        $minSize      = $this->parameter('min_size');
        $maxSize      = $this->parameter('max_size');
        $allowedTypes = $this->parameter('allowed_types');

        // below is Required rule job
        if (!$this->isValueFromUploadedFiles($value) || $value['error'] == UPLOAD_ERR_NO_FILE) {
            return true;
        }

        if (!$this->isUploadedFile($value)) {
            return false;
        }

        // just make sure there is no error
        if ($value['error']) {
            return false;
        }

        if ($minSize && $value['size'] < $this->getSizeInBytes($minSize)) {
            $this->message = 'rule.uploaded_file.min_size';

            return false;
        }

        if ($maxSize && $value['size'] > $this->getSizeInBytes($maxSize)) {
            $this->message = 'rule.uploaded_file.max_size';

            return false;
        }

        if (!empty($allowedTypes) && !in_array($this->guesser->getExtension($value['type']), $allowedTypes)) {
            $this->message = 'rule.uploaded_file.type';

            return false;
        }

        return true;
    }
}
