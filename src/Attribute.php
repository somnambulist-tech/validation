<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use function array_merge;
use function call_user_func_array;
use function count;
use function explode;
use function str_contains;
use function str_replace;

class Attribute
{
    private Validation $validation;
    private ?Attribute $parent = null;
    private RuleBag $rules;
    private string $key;
    private ?string $alias;
    private bool $required = false;
    private array $indexes = [];

    public function __construct(
        Validation $validation,
        string $key,
        string $alias = null,
        array $rules = []
    ) {
        $this->validation = $validation;
        $this->alias      = $alias;
        $this->key        = $key;
        $this->rules      = new RuleBag($this, $rules);
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
        return count($this->indexes()) > 0;
    }

    public function alias(): ?string
    {
        return $this->alias;
    }

    public function indexes(): array
    {
        return $this->indexes;
    }

    /**
     * @internal
     */
    public function setIndexes(array $indexes): void
    {
        $this->indexes = $indexes;
    }

    private function resolveSiblingKey(string $key): string
    {
        $indexes        = $this->indexes();
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

    /**
     * @internal
     */
    public function setParent(Attribute $parent): void
    {
        $this->parent = $parent;
    }

    public function rules(): RuleBag
    {
        return $this->rules;
    }

    /**
     * @internal
     */
    public function validation(): Validation
    {
        return $this->validation;
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
}
