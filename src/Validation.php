<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use Closure;
use Somnambulist\Components\Validation\Exceptions\RuleException;
use Somnambulist\Components\Validation\Rules\Contracts\BeforeValidate;
use Somnambulist\Components\Validation\Rules\Contracts\ModifyValue;
use Somnambulist\Components\Validation\Rules\Required;
use function array_merge;
use function array_splice;
use function explode;
use function get_class;
use function gettype;
use function is_numeric;
use function is_object;
use function is_scalar;
use function is_string;
use function sprintf;
use function str_contains;
use function str_getcsv;

/**
 * Class Validation
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\Validation
 */
class Validation
{
    private ErrorBag $errors;
    private Factory $factory;
    private MessageBag $messages;
    private string $separator = ':';
    private array $inputs;
    /**
     * @var array|Attribute[]
     */
    private array $attributes = [];
    private array $aliases = [];
    private array $validData = [];
    private array $invalidData = [];

    public function __construct(Factory $factory, array $inputs, array $rules)
    {
        $this->factory = $factory;
        $this->inputs  = $inputs;
        $this->messages = clone $factory->messages();
        $this->errors  = new ErrorBag();

        foreach ($rules as $attributeKey => $rule) {
            $this->addAttribute($attributeKey, $rule);
        }
    }

    public function validate(array $inputs = []): void
    {
        $this->errors = new ErrorBag();
        $this->inputs = array_merge($this->inputs, $inputs);

        foreach ($this->attributes as $attribute) {
            foreach ($attribute->rules() as $rule) {
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

        $attributeKey = $attribute->key();
        $rules        = $attribute->rules();
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
                $this->addError($attribute, $ruleValidator, $value);

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
                $validator = $this->factory->rule($rulename)->fillParameters($params);
            } elseif ($rule instanceof Rule) {
                $validator = $rule;
            } elseif ($rule instanceof Closure) {
                $validator = $this->factory->rule('callback')->fillParameters([$rule]);
            } else {
                throw RuleException::invalidRuleType(is_object($rule) ? get_class($rule) : gettype($rule));
            }

            $resolvedRules[] = $validator;
        }

        return $resolvedRules;
    }

    protected function parseRule(string $rule): array
    {
        $exp      = explode(':', $rule, 2);
        $ruleName = $exp[0];

        if (in_array($ruleName, ['matches', 'regex'])) {
            $params = [$exp[1]];
        } else {
            $params = isset($exp[1]) ? str_getcsv($exp[1]) : [];
        }

        return [$ruleName, $params];
    }

    public function getFactory(): Factory
    {
        return $this->factory;
    }

    public function getAlias(string $attributeKey): ?string
    {
        return $this->aliases[$attributeKey] ?? null;
    }

    public function setAlias(string $attributeKey, string $alias): void
    {
        $this->aliases[$attributeKey] = $alias;
    }

    public function setAliases(array $aliases): void
    {
        $this->aliases = array_merge($this->aliases, $aliases);
    }

    public function getAttribute(string $attributeKey): ?Attribute
    {
        return $this->attributes[$attributeKey] ?? null;
    }

    /**
     * Check whether given $attribute is an array attribute
     */
    protected function isArrayAttribute(Attribute $attribute): bool
    {
        return str_contains($attribute->key(), '*');
    }

    /**
     * Parse array attribute into it's child attributes
     */
    protected function parseArrayAttribute(Attribute $attribute): array
    {
        $attributeKey = $attribute->key();
        $data         = Helper::arrayDot($this->initializeAttributeOnData($attributeKey));

        $pattern = str_replace('\*', '([^\.]+)', preg_quote($attributeKey));

        $data = array_merge($data, $this->extractValuesForWildcards(
            $data,
            $attributeKey
        ));

        $attributes = [];

        foreach ($data as $key => $value) {
            if (preg_match('/^' . $pattern . '\z/', $key, $match)) {
                $attr = new Attribute($this, $key, null, $attribute->rules());
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

    protected function addError(Attribute $attribute, Rule $rule, mixed $value): void
    {
        $this->errors->add($attribute->key(), $rule->name(), $this->resolveMessage($attribute, $rule, $value));
    }

    protected function resolveAttributeName(Attribute $attribute): string
    {
        return $this->aliases[$attribute->key()] ?? $this->aliases[$attribute->getPrimaryAttribute()?->key()] ?? $attribute->key();
    }

    protected function resolveMessage(Attribute $attribute, Rule $rule, mixed $value): ErrorMessage
    {
        $primaryAttribute = $attribute->getPrimaryAttribute();
        $attributeKey     = $attribute->key();
        $ruleName         = $rule->name();
        $message          = $rule->message(['attribute' => $this->resolveAttributeName($attribute), 'value' => $value]);
        $messageKeys      = [
            $attributeKey . $this->separator . $ruleName,
            $attributeKey,
            $message->key(),
        ];

        if ($primaryAttribute) {
            $primaryAttributeKey = $primaryAttribute->key();
            // adds additional key lookups in the message keys e.g. parent.*.attribute:rule
            array_splice($messageKeys, 1, 0, $primaryAttributeKey . $this->separator . $ruleName);
            array_splice($messageKeys, 3, 0, $primaryAttributeKey);
        }

        $message->setMessage($this->messages->firstOf($messageKeys));

        // Replace key indexes
        $keyIndexes = $attribute->getKeyIndexes();

        // add placeholders for [0] or {1} to params set
        foreach ($keyIndexes as $pathIndex => $index) {
            $replacers = [sprintf('[%s]', $pathIndex) => $index];

            if (is_numeric($index)) {
                $replacers[sprintf('{%s}', $pathIndex)] = $index + 1;
            }

            $message->addParams($replacers);
        }

        return $message;
    }

    public function messages(): MessageBag
    {
        return $this->messages;
    }

    public function errors(): ErrorBag
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function passes(): bool
    {
        return $this->errors->count() === 0;
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
        $key = $attribute->key();

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
        $key = $attribute->key();

        if ($attribute->isArrayAttribute() || $attribute->isUsingDotNotation()) {
            Helper::arraySet($this->invalidData, $key, $value);
            Helper::arrayUnset($this->validData, $key);
        } else {
            $this->invalidData[$key] = $value;
        }
    }
}
