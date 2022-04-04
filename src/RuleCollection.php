<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use InvalidArgumentException;
use Somnambulist\Components\Validation\Rules\Contracts\BeforeValidate;

/**
 * Class RuleBag
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\RuleBag
 */
class RuleCollection extends Collection
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
}
