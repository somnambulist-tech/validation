<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use Closure;
use Somnambulist\Components\Validation\Exceptions\RuleException;
use Somnambulist\Components\Validation\Rules\Callback;
use Somnambulist\Components\Validation\Rules\Contracts\ModifyValue;
use Somnambulist\Components\Validation\Rules\Required;

use function array_merge;
use function array_splice;
use function array_unique;
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
 * Holds the validation rules to apply to an input
 *
 * Validation instances are created by the {@link Factory} class, however can be directly
 * instantiated if required. The rules are provided either as an array of strings, or nested
 * arrays that can contain classes / closure for the rule to apply.
 *
 * See the main documentation for more on how to structure the rules / add validation rules.
 */
class Validation
{
    private AttributeBag $attributes;
    private ErrorBag $errors;
    private Factory $factory;
    private InputBag $input;
    private MessageBag $messages;
    private array $aliases = [];
    private array $validData = [];
    private array $invalidData = [];
    private string $separator = ':';
    private ?string $lang = null;

    public function __construct(Factory $factory, array $inputs, array $rules)
    {
        $this->factory    = $factory;
        $this->messages   = clone $factory->messages();
        $this->errors     = new ErrorBag();
        $this->input      = new InputBag($inputs);
        $this->attributes = new AttributeBag();

        foreach ($rules as $attributeKey => $rule) {
            $this->addAttribute($attributeKey, $rule);
        }
    }

    public function validate(array $inputs = []): void
    {
        $this->errors = new ErrorBag();
        $this->input->merge($inputs);

        $this->attributes->beforeValidate();

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

        if ($attribute->rules()->has('sometimes') && !$this->input->has($attribute->key())) {
            return;
        }

        $value        = $this->input->get($attribute->key());
        $isEmptyValue = $this->isEmptyValue($value);
        $rules        = ($attribute->rules()->has('nullable') && $isEmptyValue) ? [] : $attribute->rules();

        $isValid = true;

        foreach ($rules as $ruleValidator) {
            $ruleValidator->setAttribute($attribute);

            if ($ruleValidator instanceof ModifyValue) {
                $value        = $ruleValidator->modifyValue($value);
                $isEmptyValue = $this->isEmptyValue($value);
            }

            $valid = $ruleValidator->check($value);

            if ($isEmptyValue && $this->ruleIsOptional($attribute, $ruleValidator)) {
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

        $this->attributes->add($key, new Attribute($this, $key, $this->alias($key), $this->resolveRules($rules)));
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

    public function factory(): Factory
    {
        return $this->factory;
    }

    public function alias(string $attributeKey): ?string
    {
        return $this->aliases[$attributeKey] ?? null;
    }

    public function setAlias(string $attributeKey, string $alias): self
    {
        $this->aliases[$attributeKey] = $alias;

        return $this;
    }

    public function setLanguage(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function attributes(): AttributeBag
    {
        return $this->attributes;
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
        $pattern      = str_replace('\*', '([^\.]+)', preg_quote($attributeKey));

        $data = array_merge($data, $this->extractValuesForWildcards(
            $data,
            $attributeKey
        ));

        $attributes = [];

        foreach ($data as $key => $value) {
            if (preg_match('/^' . $pattern . '\z/', $key, $match)) {
                $attr = new Attribute($this, $key, null, $attribute->rules()->all());
                $attr->setParent($attribute);
                $attr->setIndexes(array_slice($match, 1));
                $attributes[] = $attr;
            }
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
     * Used to extract a subsection of the data for faster iteration.
     */
    protected function extractDataFromPath(?string $attributeKey): array
    {
        $results = [];

        $value = $this->input->get($attributeKey, '__missing__');

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
    private function extractValuesForWildcards(array $data, string $attributeKey): array
    {
        $keys = [];

        $pattern = str_replace('\*', '[^\.]+', preg_quote($attributeKey));

        foreach ($data as $key => $value) {
            if (preg_match('/^' . $pattern . '/', $key, $matches)) {
                $keys[] = $matches[0];
            }
        }

        return $this->input->only(...array_unique($keys));
    }

    protected function isEmptyValue(mixed $value): bool
    {
        return false === (new Required)->check($value);
    }

    protected function ruleIsOptional(Attribute $attribute, Rule $rule): bool
    {
        return
            false === $attribute->isRequired() &&
            false === $rule->isImplicit() &&
            false === $rule instanceof Required
        ;
    }

    protected function addError(Attribute $attribute, Rule $rule, mixed $value): void
    {
        $this->errors->add($attribute->key(), $rule->name(), $this->resolveMessage($attribute, $rule, $value));
    }

    protected function resolveAttributeName(Attribute $attribute): string
    {
        return $this->aliases[$attribute->key()] ?? $this->aliases[$attribute->parent()?->key()] ?? $attribute->key();
    }

    protected function resolveMessage(Attribute $attribute, Rule $rule, mixed $value): ErrorMessage
    {
        $primaryAttribute = $attribute->parent();
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

        $message->setMessage(
            $this->messages->hasAnyOf($messageKeys, $this->lang)
                ?
                $this->messages->firstOf($messageKeys, $this->lang) : $message->key()
        );

        // Replace key indexes
        $keyIndexes = $attribute->indexes();

        // add placeholders for [0] or {1} to params set
        foreach ($keyIndexes as $pathIndex => $index) {
            $replacers = [sprintf('[%s]', $pathIndex) => $index];

            if (is_numeric($index)) {
                $replacers[sprintf('{%s}', $pathIndex)] = $index + 1;
            }

            $message->addParams($replacers);
        }

        $message->addParams($attribute->rules()->parameters()->all());

        return $message;
    }

    public function input(): InputBag
    {
        return $this->input;
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
