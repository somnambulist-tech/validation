<?php

namespace Somnambulist\Components\Validation\Rules;

use Somnambulist\Components\Validation\Rule;

class EndsWith extends Rule
{

    protected string $message = 'rule.starts_with';
    protected array $fillableParams = ['compare_with'];

    public function check(mixed $value): bool{
        $this->assertHasRequiredParameters($this->fillableParams);
        $compare_with = $this->parameter('compare_with');

        if(is_string($value) || is_numeric($value)){
            $value = strval($value);
            return str_ends_with($value, $compare_with);
        }

        if(is_array($value)){
            if($this->isAssociativeArray($value)){
                $last_value = $value[array_key_last($value)];
            }else{
                $last_value = $value[sizeof($value)-1];
            }
            return $last_value === $compare_with;
        }

        return false;
    }

    /**
     * @param array $value
     * @return bool
     */
    private function isAssociativeArray(array $value): bool
    {
        if (array() === $value) return false;
        return array_keys($value) !== range(0, count($value) - 1);
    }
}