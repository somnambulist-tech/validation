<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

/**
 * Class Required
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Required
 */
class Required extends Rule
{
    use Traits\FileTrait;

    protected bool $implicit = true;
    protected string $message = "The :attribute is required";

    public function check($value): bool
    {
        $this->setAttributeAsRequired();

        if ($this->attribute?->hasRule('uploaded_file')) {
            return $this->isValueFromUploadedFiles($value) and $value['error'] != UPLOAD_ERR_NO_FILE;
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
