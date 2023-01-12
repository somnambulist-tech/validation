<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use InvalidArgumentException;
use Somnambulist\Components\Validation\Rules\Contracts\BeforeValidate;

/**
 * @property array<int, Rule>
 */
class RuleBag extends DataBag
{
    public function __construct(private Attribute $attribute, array $data = [])
    {
        parent::__construct();

        foreach ($data as $rule) {
            $this->add($rule);
        }
    }

    public function add(Rule $rule): void
    {
        $this->set($rule->name(), $rule);
    }

    public function set(string $key, mixed $value): static
    {
        if (!$value instanceof Rule) {
            throw new InvalidArgumentException('Value must be an instance of ' . Rule::class);
        }

        $value->setAttribute($this->attribute);
        $value->setValidation($this->attribute->validation());

        return parent::set($key, $value);
    }

    public function beforeValidate(): self
    {
        $this
            ->filter(fn (Rule $r) => $r instanceof BeforeValidate)
            ->each(fn (BeforeValidate $r) => $r->beforeValidate())
        ;

        return $this;
    }

    public function parameters(): DataBag
    {
        return $this->map(fn (Rule $r) => $r->parameters())->notEmpty()->flatten();
    }
}
