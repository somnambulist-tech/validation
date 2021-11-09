<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Helper;
use Somnambulist\Components\Validation\Rule;

/**
 * Class Extension
 *
 * @package    Somnambulist\Components\Validation\Rules
 * @subpackage Somnambulist\Components\Validation\Rules\Extension
 */
class Extension extends Rule
{
    protected string $message = "The :attribute must be a :allowed_extensions file";

    public function fillParameters(array $params): self
    {
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        $this->params['allowed_extensions'] = $params;

        return $this;
    }

    public function check($value): bool
    {
        $this->requireParameters(['allowed_extensions']);

        $allowedExtensions = $this->parameter('allowed_extensions');
        foreach ($allowedExtensions as $key => $ext) {
            $allowedExtensions[$key] = ltrim($ext, '.');
        }

        $or                    = $this->validation ? $this->validation->getTranslation('or') : 'or';
        $allowedExtensionsText = Helper::join(Helper::wraps($allowedExtensions, ".", ""), ', ', ", {$or} ");
        $this->setParameterText('allowed_extensions', $allowedExtensionsText);

        $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));

        return $ext && in_array($ext, $allowedExtensions);
    }
}
