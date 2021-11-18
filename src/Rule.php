<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use function array_merge;

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
    protected array $fillableParams = [];
    protected string $message = 'rule.default';

    abstract public function check(mixed $value): bool;

    public function attribute(): ?Attribute
    {
        return $this->attribute;
    }

    /**
     * @internal
     */
    public function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @internal
     */
    public function setValidation(Validation $validation): void
    {
        $this->validation = $validation;
    }

    public function parameters(): array
    {
        return $this->params;
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

    public function isImplicit(): bool
    {
        return $this->implicit;
    }

    public function message(array $params = []): ErrorMessage
    {
        $params = array_merge(
            [
                'attribute' => $this->attribute->alias() ?? $this->attribute->key(),
                'value'     => $this->attribute->value()
            ],
            $this->convertParametersForMessage(),
            $params,
        );

        return new ErrorMessage($this->message, $params);
    }

    protected function convertParametersForMessage(): array
    {
        return $this->params;
    }

    protected function assertHasRequiredParameters(array $params): void
    {
        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                throw ParameterException::missing($this->name(), $param);
            }
        }
    }

    public function name(): string
    {
        return $this->name ?: get_class($this);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
