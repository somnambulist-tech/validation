<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

/**
 * Class Attribute
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\Attribute
 */
class Attribute
{
    protected array $rules = [];
    protected string $key;
    protected ?string $alias;
    protected Validation $validation;
    protected bool $required = false;
    protected ?Attribute $primaryAttribute = null;
    protected array $otherAttributes = [];
    protected array $keyIndexes = [];

    public function __construct(
        Validation $validation,
        string $key,
        $alias = null,
        array $rules = []
    ) {
        $this->validation = $validation;
        $this->alias      = $alias;
        $this->key        = $key;

        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    public function addRule(Rule $rule): void
    {
        $rule->setAttribute($this);
        $rule->setValidation($this->validation);
        $this->rules[$rule->getKey()] = $rule;
    }

    public function getOtherAttributes(): array
    {
        return $this->otherAttributes;
    }

    public function setOtherAttributes(array $otherAttributes): void
    {
        $this->otherAttributes = [];
        foreach ($otherAttributes as $otherAttribute) {
            $this->addOtherAttribute($otherAttribute);
        }
    }

    public function addOtherAttribute(Attribute $otherAttribute): void
    {
        $this->otherAttributes[] = $otherAttribute;
    }

    public function getRule(string $ruleKey)
    {
        return $this->hasRule($ruleKey) ? $this->rules[$ruleKey] : null;
    }

    public function hasRule(string $ruleKey): bool
    {
        return isset($this->rules[$ruleKey]);
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getValue(string $key = null): mixed
    {
        if ($key && $this->isArrayAttribute()) {
            $key = $this->resolveSiblingKey($key);
        }

        if (!$key) {
            $key = $this->getKey();
        }

        return $this->validation->getValue($key);
    }

    public function isArrayAttribute(): bool
    {
        return count($this->getKeyIndexes()) > 0;
    }

    public function getKeyIndexes(): array
    {
        return $this->keyIndexes;
    }

    public function setKeyIndexes(array $keyIndexes): void
    {
        $this->keyIndexes = $keyIndexes;
    }

    public function resolveSiblingKey(string $key): string
    {
        $indexes        = $this->getKeyIndexes();
        $keys           = explode("*", $key);
        $countAsterisks = count($keys) - 1;

        if (count($indexes) < $countAsterisks) {
            $indexes = array_merge($indexes, array_fill(0, $countAsterisks - count($indexes), "*"));
        }

        $args = array_merge([str_replace("*", "%s", $key)], $indexes);

        return call_user_func_array('sprintf', $args);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function isUsingDotNotation(): bool
    {
        return str_contains($this->getKey(), '.');
    }

    public function getHumanizedKey(): string
    {
        $primaryAttribute = $this->getPrimaryAttribute();
        $key              = str_replace('_', ' ', $this->key);

        // Resolve key from array validation
        if ($primaryAttribute) {
            $split = explode('.', $key);
            $key   = implode(' ', array_map(function ($word) {
                if (is_numeric($word)) {
                    $word = $word + 1;
                }

                return Helper::snakeCase((string)$word, ' ');
            }, $split));
        }

        return ucfirst($key);
    }

    public function getPrimaryAttribute(): ?Attribute
    {
        return $this->primaryAttribute;
    }

    public function setPrimaryAttribute(Attribute $primaryAttribute): void
    {
        $this->primaryAttribute = $primaryAttribute;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }
}
