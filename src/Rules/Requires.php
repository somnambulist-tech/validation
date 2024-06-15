<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

class Requires extends Required
{
    protected bool $implicit = true;
    protected string $message = 'rule.requires';

    public function fillParameters(array $params): self
    {
        $this->params['fields'] = $params;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['fields']);

        $fields = $this->parameter('fields');

        foreach ($fields as $field) {
            if (!$this->validation->input()->has($field) || empty($this->validation->input()->get($field))) {
                return false;
            }
        }

        return true;
    }
}
