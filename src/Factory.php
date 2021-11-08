<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation;

use Somnambulist\Components\Validation\Exceptions\RuleException;

/**
 * Class Validator
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\Validator
 */
class Factory
{
    use Traits\TranslationsTrait;
    use Traits\MessagesTrait;

    private array $validators = [];
    private bool $allowRuleOverride = false;
    private bool $useHumanizedKeys = true;

    public function __construct(array $messages = [])
    {
        $this->messages = $messages;

        $this->registerBaseValidators();
    }

    protected function registerBaseValidators()
    {
        $baseValidator = [
            'required'             => new Rules\Required,
            'required_if'          => new Rules\RequiredIf,
            'required_unless'      => new Rules\RequiredUnless,
            'required_with'        => new Rules\RequiredWith,
            'required_without'     => new Rules\RequiredWithout,
            'required_with_all'    => new Rules\RequiredWithAll,
            'required_without_all' => new Rules\RequiredWithoutAll,
            'email'                => new Rules\Email,
            'alpha'                => new Rules\Alpha,
            'numeric'              => new Rules\Numeric,
            'alpha_num'            => new Rules\AlphaNum,
            'alpha_dash'           => new Rules\AlphaDash,
            'alpha_spaces'         => new Rules\AlphaSpaces,
            'in'                   => new Rules\In,
            'not_in'               => new Rules\NotIn,
            'min'                  => new Rules\Min,
            'max'                  => new Rules\Max,
            'between'              => new Rules\Between,
            'url'                  => new Rules\Url,
            'integer'              => new Rules\Integer,
            'boolean'              => new Rules\Boolean,
            'ip'                   => new Rules\Ip,
            'ipv4'                 => new Rules\Ipv4,
            'ipv6'                 => new Rules\Ipv6,
            'extension'            => new Rules\Extension,
            'array'                => new Rules\TypeArray,
            'same'                 => new Rules\Same,
            'regex'                => new Rules\Regex,
            'date'                 => new Rules\Date,
            'accepted'             => new Rules\Accepted,
            'present'              => new Rules\Present,
            'different'            => new Rules\Different,
            'uploaded_file'        => new Rules\UploadedFile,
            'mimes'                => new Rules\Mimes,
            'callback'             => new Rules\Callback,
            'before'               => new Rules\Before,
            'after'                => new Rules\After,
            'lowercase'            => new Rules\Lowercase,
            'uppercase'            => new Rules\Uppercase,
            'json'                 => new Rules\Json,
            'digits'               => new Rules\Digits,
            'digits_between'       => new Rules\DigitsBetween,
            'defaults'             => new Rules\Defaults,
            'default'              => new Rules\Defaults,
            'nullable'             => new Rules\Nullable,
        ];

        foreach ($baseValidator as $key => $validator) {
            $this->setValidator($key, $validator);
        }
    }

    private function setValidator(string $key, Rule $rule): void
    {
        $this->validators[$key] = $rule;
        $rule->setKey($key);
    }

    public function validate(array $inputs, array $rules, array $messages = []): Validation
    {
        $validation = $this->make($inputs, $rules, $messages);
        $validation->validate();

        return $validation;
    }

    public function make(array $inputs, array $rules, array $messages = []): Validation
    {
        $messages   = array_merge($this->messages, $messages);
        $validation = new Validation($this, $inputs, $rules, $messages);
        $validation->setTranslations($this->getTranslations());

        return $validation;
    }

    public function __invoke(string $rule, ...$args): Rule
    {
        $validator = $this->getValidator($rule);

        $clonedValidator = clone $validator;
        $clonedValidator->fillParameters($args);

        return $clonedValidator;
    }

    public function getValidator(string $rule): mixed
    {
        if (null !== $v = $this->validators[$rule] ?? null) {
            return $v;
        }

        throw RuleException::notFound($rule);
    }

    public function addValidator(string $ruleName, Rule $rule): void
    {
        if (!$this->allowRuleOverride && array_key_exists($ruleName, $this->validators)) {
            throw RuleException::cannotOverrideExistingRule($ruleName);
        }

        $this->setValidator($ruleName, $rule);
    }

    /**
     * Allow already defined rules to be overridden or not
     */
    public function allowRuleOverride(bool $status = false): void
    {
        $this->allowRuleOverride = $status;
    }

    /**
     * Toggle whether to use humanised rule names or not e.g. required_if => Required if
     */
    public function useHumanizedKeys(bool $useHumanizedKeys = true): void
    {
        $this->useHumanizedKeys = $useHumanizedKeys;
    }

    public function isUsingHumanizedKey(): bool
    {
        return $this->useHumanizedKeys;
    }
}
