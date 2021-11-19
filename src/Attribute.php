<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use function array_merge;
use function call_user_func_array;
use function count;
use function explode;
use function str_contains;
use function str_replace;

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
    protected ?Attribute $parent = null;
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

    public function rule(string $ruleKey)
    {
        return $this->hasRule($ruleKey) ? $this->rules[$ruleKey] : null;
    }

    public function hasRule(string $ruleKey): bool
    {
        return isset($this->rules[$ruleKey]);
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function addRule(Rule $rule): void
    {
        $rule->setAttribute($this);
        $rule->setValidation($this->validation);

        $this->rules[$rule->name()] = $rule;
    }

    public function makeRequired(): void
    {
        $this->required = true;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isArrayAttribute(): bool
    {
        return count($this->getKeyIndexes()) > 0;
    }

    public function value(string $key = null): mixed
    {
        if ($key && $this->isArrayAttribute()) {
            $key = $this->resolveSiblingKey($key);
        }

        if (!$key) {
            $key = $this->key();
        }

        return $this->validation->input()->get($key);
    }

    public function getKeyIndexes(): array
    {
        return $this->keyIndexes;
    }

    public function setKeyIndexes(array $keyIndexes): void
    {
        $this->keyIndexes = $keyIndexes;
    }

    private function resolveSiblingKey(string $key): string
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

    public function key(): string
    {
        return $this->key;
    }

    public function isUsingDotNotation(): bool
    {
        return str_contains($this->key(), '.');
    }

    public function parent(): ?Attribute
    {
        return $this->parent;
    }

    public function setParent(Attribute $parent): void
    {
        $this->parent = $parent;
    }

    public function alias(): ?string
    {
        return $this->alias;
    }
}
