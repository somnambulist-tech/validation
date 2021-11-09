<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use Closure;
use Somnambulist\Components\Validation\Exceptions\RuleException;
use Somnambulist\Components\Validation\Rules\Interfaces\BeforeValidate;
use Somnambulist\Components\Validation\Rules\Interfaces\ModifyValue;
use Somnambulist\Components\Validation\Rules\Required;
use function array_merge;
use function call_user_func_array;
use function explode;
use function get_class;
use function gettype;
use function is_numeric;
use function is_object;
use function is_scalar;
use function is_string;
use function str_contains;

/**
 * Class Validation
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\Validation
 */
class Validation
{
    use Traits\TranslationsTrait;
    use Traits\MessagesTrait;

    private ErrorBag $errors;
    private Factory $factory;
    private array $inputs;
    private array $attributes = [];
    private array $aliases = [];
    private string $messageSeparator = ':';
    private array $validData = [];
    private array $invalidData = [];

    public function __construct(Factory $validator, array $inputs, array $rules, array $messages = [])
    {
        $this->factory  = $validator;
        $this->inputs   = $inputs;
        $this->messages = $messages;
        $this->errors   = new ErrorBag;

        foreach ($rules as $attributeKey => $rule) {
            $this->addAttribute($attributeKey, $rule);
        }
    }

    protected function addAttribute(string $key, $rules): void
    {
        if (str_contains($key, ':')) {
            [$key, $alias] = explode(':', $key);
            $this->aliases[$key] = $alias;
        }

        $this->attributes[$key] = new Attribute($this, $key, $this->getAlias($key), $this->resolveRules($rules));
    }

    protected function resolveRules(array|string $rules): array
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        $resolvedRules = [];

        foreach ($rules as $i => $rule) {
            if (empty($rule)) {
                continue;
            }

            if (is_string($i) && is_scalar($rule)) {
                $rule = sprintf('%s:%s', $i, $rule);
            }

            if (is_string($rule)) {
                [$rulename, $params] = $this->parseRule($rule);
                $validator = $this->factory->getRule($rulename)->fillParameters($params);
            } elseif ($rule instanceof Rule) {
                $validator = $rule;
            } elseif ($rule instanceof Closure) {
                $validator = $this->factory->getRule('callback')->fillParameters([$rule]);
            } else {
                throw RuleException::invalidRuleType(is_object($rule) ? get_class($rule) : gettype($rule));
            }

            $resolvedRules[] = $validator;
        }

        return $resolvedRules;
    }

    public function getFactory(): Factory
    {
        return $this->factory;
    }

    protected function parseRule(string $rule): array
    {
        $exp      = explode(':', $rule, 2);
        $ruleName = $exp[0];

        if ($ruleName !== 'regex') {
            $params = isset($exp[1]) ? explode(',', $exp[1]) : [];
        } else {
            $params = [$exp[1]];
        }

        return [$ruleName, $params];
    }

    public function getAlias(string $attributeKey): ?string
    {
        return $this->aliases[$attributeKey] ?? null;
    }

    public function getAttribute(string $attributeKey): ?Attribute
    {
        return $this->attributes[$attributeKey] ?? null;
    }

    public function validate(array $inputs = []): void
    {
        $this->errors = new ErrorBag;
        $this->inputs = array_merge($this->inputs, $inputs);

        foreach ($this->attributes as $attribute) {
            foreach ($attribute->getRules() as $rule) {
                if ($rule instanceof BeforeValidate) {
                    $rule->beforeValidate();
                }
            }
        }

        foreach ($this->attributes as $attribute) {
            $this->validateAttribute($attribute);
        }
    }

    protected function validateAttribute(Attribute $attribute): void
    {
        if ($this->isArrayAttribute($attribute)) {
            $attributes = $this->parseArrayAttribute($attribute);

            foreach ($attributes as $attr) {
                $this->validateAttribute($attr);
            }

            return;
        }

        $attributeKey = $attribute->getKey();
        $rules        = $attribute->getRules();
        $value        = $this->getValue($attributeKey);
        $isEmptyValue = $this->isEmptyValue($value);

        if ($attribute->hasRule('nullable') && $isEmptyValue) {
            $rules = [];
        }

        $isValid = true;
        foreach ($rules as $ruleValidator) {
            $ruleValidator->setAttribute($attribute);

            if ($ruleValidator instanceof ModifyValue) {
                $value        = $ruleValidator->modifyValue($value);
                $isEmptyValue = $this->isEmptyValue($value);
            }

            $valid = $ruleValidator->check($value);

            if ($isEmptyValue and $this->ruleIsOptional($attribute, $ruleValidator)) {
                continue;
            }

            if (!$valid) {
                $isValid = false;
                $this->addError($attribute, $value, $ruleValidator);
                if ($ruleValidator->isImplicit()) {
                    break;
                }
            }
        }

        if ($isValid) {
            $this->setValidData($attribute, $value);
        } else {
            $this->setInvalidData($attribute, $value);
        }
    }

    /**
     * Check whether given $attribute is an array attribute
     */
    protected function isArrayAttribute(Attribute $attribute): bool
    {
        return str_contains($attribute->getKey(), '*');
    }

    /**
     * Parse array attribute into it's child attributes
     */
    protected function parseArrayAttribute(Attribute $attribute): array
    {
        $attributeKey = $attribute->getKey();
        $data         = Helper::arrayDot($this->initializeAttributeOnData($attributeKey));

        $pattern = str_replace('\*', '([^\.]+)', preg_quote($attributeKey));

        $data = array_merge($data, $this->extractValuesForWildcards(
            $data,
            $attributeKey
        ));

        $attributes = [];

        foreach ($data as $key => $value) {
            if (preg_match('/^' . $pattern . '\z/', $key, $match)) {
                $attr = new Attribute($this, $key, null, $attribute->getRules());
                $attr->setPrimaryAttribute($attribute);
                $attr->setKeyIndexes(array_slice($match, 1));
                $attributes[] = $attr;
            }
        }

        // set other attributes to each attributes
        foreach ($attributes as $i => $attr) {
            $otherAttributes = $attributes;
            unset($otherAttributes[$i]);
            $attr->setOtherAttributes($otherAttributes);
        }

        return $attributes;
    }

    /**
     * Gather a copy of the attribute data filled with any missing attributes.
     *
     * Adapted from: https://github.com/illuminate/validation/blob/v5.3.23/Validator.php#L334
     */
    protected function initializeAttributeOnData(string $attributeKey): array
    {
        $explicitPath = $this->getLeadingExplicitAttributePath($attributeKey);

        $data = $this->extractDataFromPath($explicitPath);

        $asteriskPos = strpos($attributeKey, '*');

        if (false === $asteriskPos || $asteriskPos === (mb_strlen($attributeKey, 'UTF-8') - 1)) {
            return $data;
        }

        return Helper::arraySet($data, $attributeKey, null);
    }

    /**
     * Get the explicit part of the attribute name.
     *
     * Adapted from: https://github.com/illuminate/validation/blob/v5.3.23/Validator.php#L2817
     *
     * E.g. 'foo.bar.*.baz' -> 'foo.bar'
     *
     * Allows skipping flattened data for some operations.
     */
    protected function getLeadingExplicitAttributePath(string $attributeKey): ?string
    {
        return rtrim(explode('*', $attributeKey)[0], '.') ?: null;
    }

    /**
     * Extract data based on the given dot-notated path.
     *
     * Adapted from: https://github.com/illuminate/validation/blob/v5.3.23/Validator.php#L2830
     *
     * Used to extract a sub-section of the data for faster iteration.
     */
    protected function extractDataFromPath(?string $attributeKey): array
    {
        $results = [];

        $value = Helper::arrayGet($this->inputs, $attributeKey, '__missing__');

        if ($value != '__missing__') {
            Helper::arraySet($results, $attributeKey, $value);
        }

        return $results;
    }

    /**
     * Get all the attribute values for a given wildcard attribute.
     *
     * Adapted from: https://github.com/illuminate/validation/blob/v5.3.23/Validator.php#L354
     */
    public function extractValuesForWildcards(array $data, string $attributeKey): array
    {
        $keys = [];

        $pattern = str_replace('\*', '[^\.]+', preg_quote($attributeKey));

        foreach ($data as $key => $value) {
            if (preg_match('/^' . $pattern . '/', $key, $matches)) {
                $keys[] = $matches[0];
            }
        }

        $keys = array_unique($keys);

        $data = [];

        foreach ($keys as $key) {
            $data[$key] = Helper::arrayGet($this->inputs, $key);
        }

        return $data;
    }

    public function getValue(string $key): mixed
    {
        return Helper::arrayGet($this->inputs, $key);
    }

    protected function isEmptyValue(mixed $value): bool
    {
        return false === (new Required)->check($value);
    }

    protected function ruleIsOptional(Attribute $attribute, Rule $rule): bool
    {
        return false === $attribute->isRequired() and
               false === $rule->isImplicit() and
               false === $rule instanceof Required;
    }

    protected function addError(Attribute $attribute, $value, Rule $ruleValidator): void
    {
        $ruleName = $ruleValidator->getKey();
        $message  = $this->resolveMessage($attribute, $value, $ruleValidator);

        $this->errors->add($attribute->getKey(), $ruleName, $message);
    }

    protected function resolveMessage(Attribute $attribute, $value, Rule $validator): string
    {
        $primaryAttribute = $attribute->getPrimaryAttribute();
        $params           = array_merge($validator->getParameters(), $validator->getParametersTexts());
        $attributeKey     = $attribute->getKey();
        $ruleKey          = $validator->getKey();
        $alias            = $attribute->getAlias() ?: $this->resolveAttributeName($attribute);
        $message          = $validator->getMessage();
        $messageKeys      = [
            $attributeKey . $this->messageSeparator . $ruleKey,
            $attributeKey,
            $ruleKey,
        ];

        if ($primaryAttribute) {
            $primaryAttributeKey = $primaryAttribute->getKey();
            array_splice($messageKeys, 1, 0, $primaryAttributeKey . $this->messageSeparator . $ruleKey);
            array_splice($messageKeys, 3, 0, $primaryAttributeKey);
        }

        foreach ($messageKeys as $key) {
            if (isset($this->messages[$key])) {
                $message = $this->messages[$key];
                break;
            }
        }

        // Replace message params
        $vars = array_merge($params, [
            'attribute' => $alias,
            'value'     => $value,
        ]);

        foreach ($vars as $key => $value) {
            $value   = $this->stringify($value);
            $message = str_replace(':' . $key, $value, $message);
        }

        // Replace key indexes
        $keyIndexes = $attribute->getKeyIndexes();
        foreach ($keyIndexes as $pathIndex => $index) {
            $replacers = [
                "[{$pathIndex}]" => $index,
            ];

            if (is_numeric($index)) {
                $replacers["{{$pathIndex}}"] = $index + 1;
            }

            $message = str_replace(array_keys($replacers), array_values($replacers), $message);
        }

        return $message;
    }

    protected function resolveAttributeName(Attribute $attribute): string
    {
        $primaryAttribute = $attribute->getPrimaryAttribute();

        if (isset($this->aliases[$attribute->getKey()])) {
            return $this->aliases[$attribute->getKey()];
        } elseif ($primaryAttribute and isset($this->aliases[$primaryAttribute->getKey()])) {
            return $this->aliases[$primaryAttribute->getKey()];
        } else {
            return $attribute->getKey();
        }
    }

    protected function stringify(mixed $value): string
    {
        if (is_string($value) || is_numeric($value)) {
            return (string)$value;
        } elseif (is_array($value) || is_object($value)) {
            return json_encode($value);
        } else {
            return '';
        }
    }

    public function errors(): ErrorBag
    {
        return $this->errors;
    }

    public function setAlias(string $attributeKey, string $alias): void
    {
        $this->aliases[$attributeKey] = $alias;
    }

    public function setAliases(array $aliases): void
    {
        $this->aliases = array_merge($this->aliases, $aliases);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function passes(): bool
    {
        return $this->errors->count() == 0;
    }

    /**
     * Set an input value explicitly
     */
    public function setValue(string $key, mixed $value): void
    {
        Helper::arraySet($this->inputs, $key, $value);
    }

    /**
     * Returns true if the input key exists
     */
    public function hasValue(string $key): bool
    {
        return Helper::arrayHas($this->inputs, $key);
    }

    public function getValidatedData(): array
    {
        return array_merge($this->validData, $this->invalidData);
    }

    public function getValidData(): array
    {
        return $this->validData;
    }

    protected function setValidData(Attribute $attribute, $value): void
    {
        $key = $attribute->getKey();
        if ($attribute->isArrayAttribute() || $attribute->isUsingDotNotation()) {
            Helper::arraySet($this->validData, $key, $value);
            Helper::arrayUnset($this->invalidData, $key);
        } else {
            $this->validData[$key] = $value;
        }
    }

    public function getInvalidData(): array
    {
        return $this->invalidData;
    }

    protected function setInvalidData(Attribute $attribute, $value): void
    {
        $key = $attribute->getKey();
        if ($attribute->isArrayAttribute() || $attribute->isUsingDotNotation()) {
            Helper::arraySet($this->invalidData, $key, $value);
            Helper::arrayUnset($this->validData, $key);
        } else {
            $this->invalidData[$key] = $value;
        }
    }
}
