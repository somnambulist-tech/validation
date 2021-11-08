<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use Somnambulist\Components\Validation\Exceptions\ParameterException;

/**
 * Class Rule
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\Rule
 */
abstract class Rule
{
    protected ?string $key = null;
    protected ?Attribute $attribute = null;
    protected ?Validation $validation = null;
    protected bool $implicit = false;
    protected array $params = [];
    protected array $paramsTexts = [];
    protected array $fillableParams = [];
    protected string $message = "The :attribute is invalid";

    abstract public function check(mixed $value): bool;

    public function setValidation(Validation $validation): void
    {
        $this->validation = $validation;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function getParameters(): array
    {
        return $this->params;
    }

    public function setParameters(array $params): Rule
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function setParameter(string $key, mixed $value): Rule
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function fillParameters(array $params): Rule
    {
        foreach ($this->fillableParams as $key) {
            if (empty($params)) {
                break;
            }
            $this->params[$key] = array_shift($params);
        }

        return $this;
    }

    /**
     * Get parameter from given $key, return null if it not exists
     */
    public function parameter(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Set parameter text that can be displayed in error message using ':param_key'
     */
    public function setParameterText(string $key, string $text): void
    {
        $this->paramsTexts[$key] = $text;
    }

    public function getParametersTexts(): array
    {
        return $this->paramsTexts;
    }

    public function isImplicit(): bool
    {
        return $this->implicit;
    }

    /**
     * Alias of setMessage
     */
    public function message(string $message): Rule
    {
        return $this->setMessage($message);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Rule
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Check $params for any that are required and missing
     *
     * @throws ParameterException
     */
    protected function requireParameters(array $params): void
    {
        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                throw ParameterException::missing($this->getKey(), $param);
            }
        }
    }

    public function getKey(): string
    {
        return $this->key ?: get_class($this);
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }
}
