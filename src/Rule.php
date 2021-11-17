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
    protected ?string $name = null;
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

    public function setParameters(array $params): self
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function setParameter(string $key, mixed $value): self
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function fillParameters(array $params): self
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
     * Get parameter from given $key, return null if it does not exist
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
    public function message(string $message): self
    {
        return $this->setMessage($message);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
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
                throw ParameterException::missing($this->getName(), $param);
            }
        }
    }

    public function getName(): string
    {
        return $this->name ?: get_class($this);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
