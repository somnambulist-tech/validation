<?php

declare(strict_types=1);

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
    protected array $rules = [];
    protected MessageCollection $messages;

    public function __construct()
    {
        $this->messages = new MessageCollection();

        $this->registerDefaultRules();
        $this->registerDefaultMessages();
    }

    public function __invoke(string $rule, ...$args): Rule
    {
        return $this->rule($rule)->fillParameters($args);
    }

    protected function registerDefaultRules()
    {
        $rules = [
            'accepted'             => new Rules\Accepted(),
            'after'                => new Rules\After(),
            'alpha'                => new Rules\Alpha(),
            'alpha_dash'           => new Rules\AlphaDash(),
            'alpha_num'            => new Rules\AlphaNum(),
            'alpha_spaces'         => new Rules\AlphaSpaces(),
            'array'                => new Rules\TypeArray(),
            'before'               => new Rules\Before(),
            'between'              => new Rules\Between(),
            'boolean'              => new Rules\TypeBoolean(),
            'callback'             => new Rules\Callback(),
            'date'                 => new Rules\Date(),
            'default'              => new Rules\Defaults(),
            'defaults'             => new Rules\Defaults(),
            'different'            => new Rules\Different(),
            'digits'               => new Rules\Digits(),
            'digits_between'       => new Rules\DigitsBetween(),
            'email'                => new Rules\Email(),
            'extension'            => new Rules\Extension(),
            'float'                => new Rules\TypeFloat(),
            'in'                   => new Rules\In(),
            'integer'              => new Rules\TypeInteger(),
            'ip'                   => new Rules\Ip(),
            'ipv4'                 => new Rules\Ipv4(),
            'ipv6'                 => new Rules\Ipv6(),
            'json'                 => new Rules\Json(),
            'lowercase'            => new Rules\Lowercase(),
            'matches'              => new Rules\Regex(),
            'max'                  => new Rules\Max(),
            'mimes'                => new Rules\Mimes(),
            'min'                  => new Rules\Min(),
            'not_in'               => new Rules\NotIn(),
            'nullable'             => new Rules\Nullable(),
            'number'               => new Rules\TypeInteger(),
            'numeric'              => new Rules\Numeric(),
            'phone'                => new Rules\PhoneNumber(),
            'present'              => new Rules\Present(),
            'prohibited'           => new Rules\Prohibited(),
            'prohibited_if'        => new Rules\ProhibitedIf(),
            'prohibited_unless'    => new Rules\ProhibitedUnless(),
            'regex'                => new Rules\Regex(),
            'rejected'             => new Rules\Rejected(),
            'required'             => new Rules\Required(),
            'required_if'          => new Rules\RequiredIf(),
            'required_unless'      => new Rules\RequiredUnless(),
            'required_with'        => new Rules\RequiredWith(),
            'required_with_all'    => new Rules\RequiredWithAll(),
            'required_without'     => new Rules\RequiredWithout(),
            'required_without_all' => new Rules\RequiredWithoutAll(),
            'same'                 => new Rules\Same(),
            'sometimes'            => new Rules\Sometimes(),
            'string'               => new Rules\TypeString(),
            'uploaded_file'        => new Rules\UploadedFile(),
            'uppercase'            => new Rules\Uppercase(),
            'url'                  => new Rules\Url(),
            'uuid'                 => new Rules\Uuid(),
        ];

        foreach ($rules as $key => $rule) {
            $this->addRule($key, $rule);
        }
    }

    protected function registerDefaultMessages(): void
    {
        $this->messages->add('en', include __DIR__ . '/Resources/i18n/en.php');
    }

    public function make(array $inputs, array $rules): Validation
    {
        return new Validation($this, $inputs, $rules);
    }

    public function validate(array $inputs, array $rules): Validation
    {
        $validation = $this->make($inputs, $rules);
        $validation->validate();

        return $validation;
    }

    /**
     * Returns the Rule instance for the key; this is a cloned instance
     *
     * @param string $rule
     *
     * @return Rule
     * @throws RuleException
     */
    public function rule(string $rule): Rule
    {
        $v = $this->rules[$rule] ?? null;

        if ($v !== null) {
            return clone $v;
        }

        throw RuleException::notFound($rule);
    }

    public function addRule(string $key, Rule $rule): void
    {
        $this->rules[$key] = $rule;
        $rule->setName($key);
    }

    public function messages(): MessageCollection
    {
        return $this->messages;
    }
}
