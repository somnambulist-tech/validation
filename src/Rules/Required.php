<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Behaviours\CanValidateFiles;

class Required extends Rule
{
    use CanValidateFiles;

    protected bool $implicit = true;
    protected string $message = 'rule.required';

    public function check(mixed $value): bool
    {
        $this->setAttributeAsRequired();

        if ($this->attribute?->rules()->has('uploaded_file')) {
            return $this->isValueFromUploadedFiles($value) && $value['error'] != UPLOAD_ERR_NO_FILE;
        }

        if (is_string($value)) {
            return mb_strlen(trim($value), 'UTF-8') > 0;
        }
        if (is_array($value)) {
            return count($value) > 0;
        }

        return !is_null($value);
    }

    protected function setAttributeAsRequired(): void
    {
        $this->attribute?->makeRequired();
    }
}
