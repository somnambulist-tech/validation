# Somnambulist Validation

[![GitHub Actions Build Status](https://img.shields.io/github/actions/workflow/status/somnambulist-tech/validation/tests.yml?logo=github&branch=main)](https://github.com/somnambulist-tech/validation/actions?query=workflow%3Atests)
[![Issues](https://img.shields.io/github/issues/somnambulist-tech/validation?logo=github)](https://github.com/somnambulist-tech/validation/issues)
[![License](https://img.shields.io/github/license/somnambulist-tech/validation?logo=github)](https://github.com/somnambulist-tech/validation/blob/master/LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/somnambulist/validation?logo=php&logoColor=white)](https://packagist.org/packages/somnambulist/validation)
[![Current Version](https://img.shields.io/packagist/v/somnambulist/validation?logo=packagist&logoColor=white)](https://packagist.org/packages/somnambulist/validation)

This is a re-write of [rakit/validation](https://github.com/rakit/validation), a standalone validator like Laravel Validation.
In keeping with rakit/validation, this library does not have any other dependencies for usage.

Please note that the internal API is substantially different to rakit/validation.

Jump to [rules](#available-rules)

## Requirements

 * PHP 8.2+
 * ext/mb-string

## Installation

Install using composer, or checkout / pull the files from github.com.

 * composer require somnambulist/validation

## Usage

There are two ways for validating data with this library: using `make` to make a validation object,
then validate it using `validate`; or use `validate`.

For example:

Using `make`:

```php
<?php

require('vendor/autoload.php');

use Somnambulist\Components\Validation\Factory;

$validation = (new Factory)->make($_POST + $_FILES, [
    'name'                  => 'required',
    'email'                 => 'required|email',
    'password'              => 'required|min:6',
    'confirm_password'      => 'required|same:password',
    'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
    'skills'                => 'array',
    'skills.*.id'           => 'required|numeric',
    'skills.*.percentage'   => 'required|numeric'
]);
$validation->validate();

if ($validation->fails()) {
    // handling errors
    $errors = $validation->errors();
    echo "<pre>";
    print_r($errors->firstOfAll());
    echo "</pre>";
    exit;
} else {
    // validation passes
    echo "Success!";
}
```

or via `validate`:

```php
<?php

require('vendor/autoload.php');

use Somnambulist\Components\Validation\Factory;

$validation = (new Factory)->validate($_POST + $_FILES, [
    'name'                  => 'required',
    'email'                 => 'required|email',
    'password'              => 'required|min:6',
    'confirm_password'      => 'required|same:password',
    'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
    'skills'                => 'array',
    'skills.*.id'           => 'required|numeric',
    'skills.*.percentage'   => 'required|numeric'
]);

if ($validation->fails()) {
	// handling errors
	$errors = $validation->errors();
	echo "<pre>";
	print_r($errors->firstOfAll());
	echo "</pre>";
	exit;
} else {
	// validation passes
	echo "Success!";
}
```

> You are strongly advised to use a Dependency Injection container and store the `Factory` as a singleton
instead of creating new instances. This will reduce the penalty for creating validation instances and allow custom
rules to be more easily managed.

### Attribute Aliases

Unlike `rakit/validation`, attribute names are not transformed in any way; instead, if you wish to name your
attributes, aliases must be used.

Aliases can be defined in several ways: on the rule itself, or by adding the alias to the validation. Note that
aliases should be set before calling `validate`.

```php
use Somnambulist\Components\Validation\Factory;

$validation = (new Factory)->make([
	'province_id' => $_POST['province_id'],
	'district_id' => $_POST['district_id']
], [
	'province_id:Province' => 'required|numeric',
	'district_id:District' => 'required|numeric'
]);

// or set the aliases:
$validation->setAlias('province_id', 'Province');
$validation->setAlias('district_id', 'District');

// then validate it
$validation->validate();
```

## Validated, Valid, and Invalid Data

After validation, the data results are held in each validation instance. For example:

```php
use Somnambulist\Components\Validation\Factory;

$validation = (new Factory())->validate([
    'title' => 'Lorem Ipsum',
    'body' => 'Lorem ipsum dolor sit amet ...',
    'published' => null,
    'something' => '-invalid-'
], [
    'title' => 'required',
    'body' => 'required',
    'published' => 'default:1|required|in:0,1',
    'something' => 'required|numeric'
]);
```

Now you can get the validated data, only the valid data, or only the invalid data:

```php
$validatedData = $validation->getValidatedData();
// [
//     'title' => 'Lorem Ipsum',
//     'body' => 'Lorem ipsum dolor sit amet ...',
//     'published' => '1' // notice this
//     'something' => '-invalid-'
// ]

$validData = $validation->getValidData();
// [
//     'title' => 'Lorem Ipsum',
//     'body' => 'Lorem ipsum dolor sit amet ...',
//     'published' => '1'
// ]

$invalidData = $validation->getInvalidData();
// [
//     'something' => '-invalid-'
// ]
```

## Available Rules

> Click to show details.

<details><summary><strong>accepted</strong></summary>

The field under this rule must be one of `'on'`, `'yes'`, `'1'`, `'true'` (the string "true"), or `true`.

</details>

<details><summary><strong>after</strong>:tomorrow</summary>

The field under this rule must be a date after the given minimum.

The parameter should be any valid string that can be parsed by `strtotime`. For example:

* after:next week
* after:2016-12-31
* after:2016
* after:2016-12-31 09:56:02

</details>

<details><summary><strong>alpha</strong></summary>

The field under this rule must be entirely alphabetic characters.

</details>

<details><summary><strong>alpha_num</strong></summary>

The field under this rule must be entirely alpha-numeric characters.

</details>

<details><summary><strong>alpha_dash</strong></summary>

The field under this rule may have alpha-numeric characters, as well as dashes and underscores.

</details>

<details><summary><strong>alpha_spaces</strong></summary>

The field under this rule may have alpha characters, as well as spaces.

</details>

<details><summary><strong>any_of</strong>:value,value,value</summary>

A variation of `in`: here the values (separated by default with a `,`) must all be in the given values.
For example: `order => 'name,date'` with the rule `any_of:name,id` would fail validation as `date` is not
part of the allowed values. The separator can be changed by calling `separator()` on the rule instance.

```php
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Rules\AnyOf;

$validation = $factory->validate([
    'field' => 'foo;bar;example'
], [
    'field' => $factory->rule('any_of')->separator(';')->values(['foo', 'bar']),
]);

$validation->passes(); // true if field only contains the values in any_of
```

Like `in`, comparisons can be performed use strict matching by calling `->strict(true)` on the rule.

This rule is useful for APIs that allow comma separated data as a single parameter e.g. JsonAPI include,
order etc. If the source is already an array, then `array|in:...` can be used instead.

</details>

<details><summary><strong>array</strong></summary>

The field under this rule must be an array.

</details>

<details><summary><strong>array_must_have_keys</strong>:value,value,value</summary>

The array must contain all the specified keys to be valid. This is useful to ensure that a
nested array meets a prescribed format. The same thing can be achieved by using individual
rules for each key with `required`. Note that this will still allow additional keys to be
present, it merely validates the presence of specific keys.

This rule is best used in conjunction with the `array` rule, though it can be used standalone.

```php
use Somnambulist\Components\Validation\Factory;

$validation = $factory->validate([
    'filters' => ['foo' => 'bar', 'baz' => 'example']
], [
    'filters' => 'array|array_must_have_keys:foo,bar,baz',
]);

$validation->passes(); // true if filters has all the keys in array_must_have_keys
```

The following examples are functionally equivalent:

```php
use Somnambulist\Components\Validation\Factory;

$validation = $factory->validate([
    'filters' => ['foo' => 'bar', 'baz' => 'example']
], [
    'filters' => 'array|array_must_have_keys:foo,bar,baz',
    'filters.foo' => 'string|between:1,50',
    'filters.bar' => 'numeric|min:1',
    'filters.baz' => 'uuid',
]);

$validation = $factory->validate([
    'filters' => ['foo' => 'bar', 'baz' => 'example']
], [
    'filters' => 'array',
    'filters.foo' => 'required|string|between:1,50',
    'filters.bar' => 'required|numeric|min:1',
    'filters.baz' => 'required|uuid',
]);
```

</details>

<details><summary><strong>array_can_only_have_keys</strong>:value,value,value</summary>

The array can only contain the specified keys, any keys not present will fail validation. By default,
associative data has no restrictions on the key => values that can be present. For example: you have
filters for a search box that are passed to SQL, only the specified keys should be allowed to be sent
and not any value in the array of filters.

This rule is best used in conjunction with the `array` rule, though it can be used standalone.

```php
use Somnambulist\Components\Validation\Factory;

$validation = $factory->validate([
    'filters' => ['foo' => 'bar', 'baz' => 'example']
], [
    'filters' => 'array|array_can_only_have_keys:foo,bar',
]);

$validation->passes(); // true if filters only has the keys in array_can_only_have_keys
```

</details>

<details><summary><strong>before</strong>:yesterday</summary>

The field under this rule must be a date before the given maximum.

This also works the same way as the [after rule](#after). Pass anything that can be parsed by `strtotime`

</details>

<details><summary><strong>between</strong>:min,max</summary>

The field under this rule must have a size between min and max params.
Value size is calculated in the same way as `min` and `max` rule.

You can also validate the size of uploaded files using this rule:

```php
$validation = $validator->validate([
    'photo' => $_FILES['photo']
], [
    'photo' => 'required|between:1M,2M'
]);
```

</details>

<details><summary><strong>boolean</strong></summary>

The field under this rule must be boolean. Accepted inputs are `true`, `false`, `1`, `0`, `"1"`, and `"0"`.

</details>

<details><summary><strong>callback</strong></summary>

Define a custom callback to validate the value. This rule cannot be registered using the string syntax.
To use this rule, you must use the array syntax and either explicitly specify `callback`, or pass the
closure:

```php
$validation = $validator->validate($_POST, [
    'even_number' => [
        'required',
        function ($value) {
            // false = invalid
            return (is_numeric($value) AND $value % 2 === 0);
        },
        'callback' => fn ($v) => is_numeric($v) && $v % 2 === 0,
    ]
]);
```

You can set a custom message by returning a string instead of false. To allow for message translation, instead of
a literal string; return a message key instead and add this to the message bag on the Factory.

> Note: returning a message string will be removed in a future version, requiring only boolean responses.
> Instead, set the message string directly before returning true/false via `$this->message = "";`.

```php
$validation = $validator->validate($_POST, [
    'even_number' => [
        'required',
        function ($value) {
            if (!is_numeric($value)) {
                return ":attribute must be numeric.";
            }
            if ($value % 2 !== 0) {
                return ":attribute is not even number.";
            }
            
            return true; // always return true if validation passes
        }
    ]
]);
```

> Note: callback closures are bound to the rule instance allowing access to rule properties via $this.

</details>

<details><summary><strong>date</strong>:format</summary>

The field under this rule must be valid date following a given format. Parameter `format` is
optional, default format is `Y-m-d`.

</details>

<details><summary><strong>default/defaults</strong></summary>

If the attribute has no value, this default will be used in place in the validated data.

For example if you have validation like this

```php
use Somnambulist\Components\Validation\Factory;

$validation = (new Factory)->validate([
    'enabled' => null
], [
    'enabled' => 'default:1|required|in:0,1'
    'published' => 'default:0|required|in:0,1'
]);

$validation->passes(); // true

// Get the valid/default data
$valid_data = $validation->getValidData();

$enabled = $valid_data['enabled'];
$published = $valid_data['published'];
```

Validation passes because the default value for `enabled` and `published` is set to `1` and `0` which is valid.

</details>

<details><summary><strong>different</strong>:another_field</summary>

Opposite of `same`; the field value under this rule must be different to `another_field` value.

</details>

<details><summary><strong>digits</strong>:value</summary>

The field under validation must be numeric and must have an exact length of `value`.

</details>

<details><summary><strong>digits_between</strong>:min,max</summary>

The field under validation must be numeric and have a length between the given `min` and `max`.

</details>

<details><summary><strong>email</strong></summary>

The field under this validation must be a valid email address according to the built-in PHP filter extension.

See [FILTER_VALIDATE_EMAIL](https://www.php.net/manual/en/filter.filters.validate.php) for details.

</details>

<details><summary><strong>ends_with</strong>:another_field</summary>

The field under this validation must end with `another_field`. Comparison can be against strings, numbers and
array elements.

</details>

<details><summary><strong>exists</strong>:table,column (database)</summary>

The field under this validation must exist in the given table. This does not check for uniqueness,
only that at least one record for the provided value and column in the table is there. 

> To use this rule, you must provide a DBAL connection. This should be done via dependency injection. 

For example:

```php
use Somnambulist\Components\Validation\Factory;

$validation = (new Factory)->validate([
    'country' => 'GBR'
], [
    'country' => 'exists:countries,id',
]);

$validation->passes(); // true if table countries has a record with id GBR
```

For more refined validation, the underlying query may be modified by setting a closure by
calling `->where()`. The closure will be passed a `Doctrine\DBAL\Query\QueryBuilder` instance.

```php
use Doctrine\DBAL\Query\QueryBuilder;
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Rules\Exists;

$factory    = new Factory;
$factory->addRule('exists', new Exists($dbalConn));

$validation = $factory->validate([
    'country' => 'GBR'
], [
    'country' => $factory->rule('exists')->table('countries')->column('id')->where(fn (QueryBuilder $qb) => $qb->andWhere('active = 1')),
]);

$validation->passes(); // true if table countries has a record with id GBR and it is active
```

</details>

<details><summary><strong>extension</strong>:extension_a,extension_b,...</summary>

The field under this rule must end with an extension corresponding to one of those listed.

This is useful for validating a file type for a given path or url. The `mimes` rule should be used
for validating uploads.

> If you require strict mime checking you should implement a custom `MimeTypeGuesser` that
can make use of a server side file checker that uses a mime library.

</details>

<details><summary><strong>float</strong></summary>

The field under this rule must be a floating point number, for example: 0.0 12.3456 etc. The value may be a
string containing a float. Note that integers and 0 (zero) will fail validation with this rule.

</details>

<details><summary><strong>in</strong>:value_1,value_2,...</summary>

The field under this rule must be included in the given list of values.

To help build the string rule, the `In` (and `NotIn`) rules have a helper method:

```php
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Rules\In;

$factory = new Factory();
$validation = $factory->validate($data, [
    'enabled' => [
        'required',
        In::make([true, 1])
    ]
]);
```

This rule uses `in_array` to perform the validation and by default does not perform strict checking.
If you require strict checking, you can invoke the rule like this:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$validation = $factory->validate($data, [
    'enabled' => [
        'required',
        $factory->rule('in')->values([true, 1])->strict()
    ]
]);
```

Then 'enabled' value should be boolean `true`, or int `1`.

</details>

<details><summary><strong>integer</strong></summary>

The field under validation must be an integer.

</details>

<details><summary><strong>ip</strong></summary>

The field under this rule must be a valid ipv4 or ipv6 address.

</details>

<details><summary><strong>ipv4</strong></summary>

The field under this rule must be a valid ipv4 address.

</details>

<details><summary><strong>ipv6</strong></summary>

The field under this rule must be a valid ipv6 address.

</details>

<details><summary><strong>json</strong></summary>

The field under this validation must be a valid JSON string.

</details>

<details><summary><strong>length</strong>:number</summary>

The field under this validation must be a string of exactly the length specified.

</details>

<details><summary><strong>lowercase</strong></summary>

The field under this validation must be in lowercase.

</details>

<details><summary><strong>max</strong>:number</summary>

The field under this rule must have a size less than or equal to the given number.
Value size is calculated in the same way as the `min` rule.

You can also validate the maximum size of uploaded files using this rule:

```php
$validation = $validator->validate([
    'photo' => $_FILES['photo']
], [
    'photo' => 'required|max:2M'
]);
```

</details>

<details><summary><strong>mimes</strong>:extension_a,extension_b,...</summary>

The `$_FILES` item under validation must have a MIME type corresponding to one of the listed extensions.

> This works on file extension and not client sent headers or embedded file type. If you require
strict mime type validation you are recommended to implement a custom `MimeTypeGuesser` that uses a full
mime-type lookup library and replace the built-in mime rule.

Additional mime types can be added to the existing guesser by using dependency injection and keeping the
mime type guesser as a service.

</details>

<details><summary><strong>min</strong>:number</summary>

The field under this rule must have a size greater than or equal to the given number.

For string values, the size corresponds to the number of characters. For integer or float values, size
corresponds to its numerical value. For an array, size corresponds to the count of the array. If your
value is numeric string, you can use the `numeric` rule to treat its size as a numeric value instead of
the number of characters.

You can also validate the minimum size of uploaded files using this rule:

```php
$validation = $validator->validate([
    'photo' => $_FILES['photo']
], [
    'photo' => 'required|min:1M'
]);
```

</details>

<details><summary><strong>not_in</strong>:value_1,value_2,...</summary>

The field under this rule must not be included in the given list of values.

This rule also uses `in_array` and can have strict checks enabled the same way as `In`.

</details>

<details><summary><strong>nullable</strong></summary>

The field under this rule may be empty.

</details>

<details><summary><strong>numeric</strong></summary>

The field under this rule must be numeric.

</details>

<details><summary><strong>present</strong></summary>

The field under this rule must be in the set of inputs, whatever the value is.

</details>

<details><summary><strong>prohibited</strong></summary>

The field under this rule is not allowed.

</details>

<details><summary><strong>prohibited_if</strong></summary>

The field under this rule is not allowed if `another_field` is provided with any of the value(s).

</details>

<details><summary><strong>prohibited_unless</strong></summary>

The field under this rule is not allowed unless `another_field` has one of these values. This is
the inverse of `prohibited_if`.

</details>

<details><summary><strong>regex</strong>:/your-regex/</summary>

The field under this rule must match the given regex. Note: if you require the use of `|`, then
the regex rule must be written in array format instead of as a string. For example:

```php
use Somnambulist\Components\Validation\Factory;

$validation = (new Factory())->validate([
    'field' => 'value'
], [
    'field' => [
        'required',
        'regex' => '/(this|that|value)/'
    ]
])
```

</details>

<details><summary><strong>rejected</strong></summary>

The field under this rule must have a value that corresponds to rejection i.e. 0 (zero), "0", false, no,
"false", off. This is the inverse of the `accepted` rule.

</details>

<details><summary><strong>required</strong></summary>

The field under this validation must be present and not 'empty'.

Here are some examples:

| Value         | Valid |
| ------------- | ----- |
| `'something'` | true  |
| `'0'`         | true  |
| `0`           | true  |
| `[0]`         | true  |
| `[null]`      | true  |
| null          | false |
| []            | false |
| ''            | false |

For uploaded files, `$_FILES['key']['error']` must not be `UPLOAD_ERR_NO_FILE`.

</details>

<details><summary><strong>required_if</strong>:another_field,value_1,value_2,...</summary>

The field under this rule must be present and not empty if the `another_field` field is equal to any value.

For example `required_if:something,1,yes,on` will be required if `something`'s value is one of `1`, `'1'`, `'yes'`, or `'on'`.

</details>

<details><summary><strong>required_unless</strong>:another_field,value_1,value_2,...</summary>

The field under validation must be present and not empty unless the `another_field` field is equal to any value.

</details>

<details><summary><strong>requires</strong>:field_1,field_2,...</summary>

The field under validation requires that the specified fields are present in the input data and are not empty.

For example: field b "requires:a"; if a is either not present, or has an "empty" value, then the validation
fails. "empty" is false, empty string, or null.

This is an extension of `required_with`, however the rule will fail when used with `sometimes` or `nullable`.
For example: if b "requires:a" and "a" is allowed to be `nullable`, b will fail as it explicitly requires a
with a value.

</details>

<details><summary><strong>required_with</strong>:field_1,field_2,...</summary>

The field under validation must be present and not empty only if any of the other specified fields are present.

__Note__: the behaviour of this rule can be circumvented if the rule this is defined on is `sometimes` or
`nullable`. For example: if a is "required_with:b", but a is also only `sometimes` present, then the required_with
will never trigger as the sometimes rule will negate it. a would also need to be explicitly passed to trigger the
rule.

</details>

<details><summary><strong>required_without</strong>:field_1,field_2,...</summary>

The field under validation must be present and not empty only when any of the other specified fields are not present.

</details>

<details><summary><strong>required_with_all</strong>:field_1,field_2,...</summary>

The field under validation must be present and not empty only if all the other specified fields are present.

</details>

<details><summary><strong>required_without_all</strong>:field_1,field_2,...</summary>

The field under validation must be present and not empty only when all the other specified fields are not present.

</details>

<details><summary><strong>same</strong>:another_field</summary>

The field value under this rule must have the same value as `another_field`.

</details>

<details><summary><strong>sometimes</strong></summary>

The field should only be validated if present in the input data. For example: `field => sometimes|required|email`

</details>

<details><summary><strong>starts_with</strong>:another_field</summary>

The field under this validation must start with `another_field`. Comparison can be against strings, numbers and
array elements.

</details>

<details><summary><strong>string</strong></summary>

The field under this rule must be a PHP string.

</details>

<details><summary><strong>unique</strong>:table,column,ignore,ignore_column (database)</summary>

The field under this validation must be unique in the given table. Optionally: a value may be
ignored and this could be an alternative column value if the ignore_column is given.

> To use this rule, you must provide a DBAL connection. This should be done via dependency injection.

For example:

```php
use Somnambulist\Components\Validation\Factory;

$validation = (new Factory)->validate([
    'email' => 'foo@example.org'
], [
    'email' => 'email|unique:users,email',
]);

$validation->passes(); // true if table users does not contain the email
```

Ignore the current users email address:

```php
use Somnambulist\Components\Validation\Factory;

$validation = (new Factory)->validate([
    'email' => 'foo@example.org'
], [
    'email' => 'email|unique:users,email,10,id',
]);

$validation->passes(); // true if table users ignoring id 10, does not contain email
```

For more refined validation, the underlying query may be modified by setting a closure by
calling `->where()`. The closure will be passed a `Doctrine\DBAL\Query\QueryBuilder` instance.

```php
use Doctrine\DBAL\Query\QueryBuilder;
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Rules\Unique;

$factory    = new Factory;
$factory->addRule('unique', new Unique($dbalConn));

$validation = $factory->validate([
    'email' => 'foo@example.org'
], [
    'email' => $factory->rule('unique')->table('users')->column('email')->where(fn (QueryBuilder $qb) => $qb->andWhere('active = 1')),
]);

$validation->passes(); // true if table users does not contain an active email
```

</details>

<details><summary><strong>uploaded_file</strong>:min_size,max_size,extension_a,extension_b,...</summary>

This rule will validate data from `$_FILES`. The field under this rule has the following conditions:

* `$_FILES['key']['error']` must be `UPLOAD_ERR_OK` or `UPLOAD_ERR_NO_FILE`. For `UPLOAD_ERR_NO_FILE` you can validate it with `required` rule.
* If min size is given, uploaded file size **MUST NOT** be lower than min size.
* If max size is given, uploaded file size **MUST NOT** be higher than max size.
* If file types is given, mime type must be one of those given types.

For size constraints _both_ must be given when using the string definition. To specify only a max size, use
the factory to fetch the rule and use method chaining.

Here are some example definitions and explanations:

* `uploaded_file`: uploaded file is optional. When it is not empty, it must be `ERR_UPLOAD_OK`.
* `required|uploaded_file`: uploaded file is required, and it must be `ERR_UPLOAD_OK`.
* `uploaded_file:0,1M`: uploaded file size must be between 0 - 1 MB, but uploaded file is optional.
* `required|uploaded_file:0,1M,png,jpeg`: uploaded file size must be between 0 - 1MB and mime types must be `image/jpeg` or `image/png`.

For multiple file uploads, PHP uses the format `_FILES[key][name][0..n+1]` ([see PHP manual for more details](http://php.net/manual/en/features.file-upload.multiple.php#53240)).
Provided the attribute key is given using dot notation, the files array is automatically re-ordered to a
nested array of related attributes. This allows multiple files to be validated using the same rule. This
will only occur if the attribute name uses dot notation.

From 1.4.2, allowing multiple files without a dot rule will raise a `RuntimeException`.

For example if you have input files like this:

```html
<input type="file" name="photos[]"/>
<input type="file" name="photos[]"/>
<input type="file" name="photos[]"/>
```

You can validate all the files by using:

```php
$validation = (new Factory)->validate($_FILES, [
    'photos.*' => 'uploaded_file:0,2M,jpeg,png'
]);

// or

$validation = (new Factory)->validate($_FILES, [
    'photos.*' => 'uploaded_file|max:2M|mimes:jpeg,png'
]);
```

Or if you have input files like this:

```html
<input type="file" name="images[profile]"/>
<input type="file" name="images[cover]"/>
```

You can validate it like this:

```php
$validation = (new Factory)->validate($_FILES, [
    'images.*' => 'uploaded_file|max:2M|mimes:jpeg,png',
]);

// or

$validation = (new Factory)->validate($_FILES, [
    'images.profile' => 'uploaded_file|max:2M|mimes:jpeg,png',
    'images.cover' => 'uploaded_file|max:5M|mimes:jpeg,png',
]);
```
</details>

<details><summary><strong>uppercase</strong></summary>

The field under this validation must be in uppercase.

</details>

<details><summary><strong>url</strong></summary>

The field under this rule must be a valid url format. The default is to validate the common format: `any_scheme://...`.
You can specify specific URL schemes if you wish.

For example:

```php
$validation = (new Factory)->validate($inputs, [
    'random_url' => 'url',          // value can be `any_scheme://...`
    'https_url' => 'url:http',      // value must be started with `https://`
    'http_url' => 'url:http,https', // value must be started with `http://` or `https://`
    'ftp_url' => 'url:ftp',         // value must be started with `ftp://`
    'custom_url' => 'url:custom',   // value must be started with `custom://`
]);
```

> Unlike `rakit`, mailto and JDBC are not supported. Implement a custom rule or a regex to validate these.

</details>

<details><summary><strong>uuid</strong></summary>

The field under this validation must be a valid UUID and not the nil UUID string.

</details>

## Optional vs Nullable Validation

Sometimes attributes can be left off or can be null. These cases should be handled carefully and have different results
after validation.

For optional attributes that can be left out of the data under validation i.e. only validated if the data is present,
the `sometimes` rule may be used. If this is specified, then that attribute can be left out completely OR it must meet
the validation criteria.  This is very useful for things like search filters, or pagination markers that are not always
required:

```php
[
    'filters' => 'sometimes|array',
]
```

In this example `filters` is entirely optional but if specified should be an array of values. Passing `[filters => '']`
would not be valid, it would have to be: `[filters => []]`.

Sometimes instead of the attribute being optional, it should be undefined i.e. `null`. Generally it is preferable to use
`sometimes` and have the value omitted but there may be a case to maintain the attribute with a `null` value. In these
instances use the `nullable` rule. This will allow the attribute to be present without any value. For example: the
users birthday may be nullable or a date: `nullable|date`.

Unlike `rakit/validation`, the use of nullable data can cause issues as this library uses strict typing throughout.
This means that many rules that test for string, or array, or a number error because they receive `null`. This is an
ambiguity in the rule definition process. For example the rule: `name: string|max:200` as defined implicitly implies
that the `name` should be a string and up to 200 characters - `null` should not be valid, but to maintain partial
compatibility it will allow null.

The next major version of this library will remove this handling and make this type of definition _require_ that the
field be both present and have a value that is not empty (unless empty is specifically allowed). To allow null values,
the `nullable` rule will need to explicitly defined. As such it is good practice to always use nullable or sometimes.

## Validating Array Data

This library can validate complex arrays of data by making use of dot notation to define the structure of the array.
There are a couple of variations and some edge cases to be aware of to prevent issues.

The most common situation is wanting to allow an array of options similar to the examples earlier in this readme.

```php
[
    'skills'              => 'array',
    'skills.*.id'         => 'required|numeric',
    'skills.*.percentage' => 'required|numeric'
],
```

The earlier example rules are set to validate user related data and includes an array of skills. Each skill has an id
and a percentage value. In this case the parent key `skills` should have the rule `array` defined. This is needed to
ensure the data is actually an array. Each skill property is then referenced using `*` to indicate there are multiple
values within the skills attribute.

These rules would validate the following array structure:

```php
[
    'skills' => [
        [
            'id' => 3,
            'percentage' => 50,
        ],
        [
            'id' => 17,
            'percentage' => 50,
        ],
    ]
]
```

The less common situation is an array of arrays without a parent key. In this case there is no prefix and each sub-key
starts with a `*`. In this situation you should be careful not to mix standard key -> value pairs with the array data.

For example:

```php
[
    '*.id'         => 'required|numeric',
    '*.percentage' => 'required|numeric'
]
```

would be used to validate the following array structure:

```php
[
    [
        'id' => 3,
        'percentage' => 50,
    ],
    [
        'id' => 17,
        'percentage' => 50,
    ],
]
```

To avoid problems you would need to ensure that the data would not include:

```php
[
    'name' => 'foo bar',
    [
        'id' => 3,
        'percentage' => 50,
    ],
    [
        'id' => 17,
        'percentage' => 50,
    ],
]
```

### Dependent Validation Rules and Array Data

Some rules are used to determine the presence or to be required if certain keys are present. Usually these use the
standard key name e.g.: `confirm_password` should be the same as the `password` field, so the rule is written as:
`same:password`.

However: for array data this will not work as the attribute is not the name of the attribute but the _path_ for
that attribute.

Using the same skills array as an example, say we wanted to require a label if the skill is new. If this was specified
as `required_if:id:null`, then the validation would look for an attribute named `id` in the root of the data - but it
does not exist, or it may find the wrong key.

Instead: we have to explicitly bind the rule to the same skill key by writing the rule as: `required_if:skills.*.id,null`.
If we don't do this, then the rule will be ignored or fail. The same applies when using array of arrays: referencing
other fields within that array should be prefixed with a `*.` e.g. `required_if:*.id,null`.

Here are examples of both syntaxes:

```php
[
    'skills.*.id'         => 'sometimes|numeric',
    'skills.*.percentage' => 'required|numeric',
    'skills.*.title'      => 'required_if:skills.*.id,null|string',
]
```

And array of arrays:

```php
[
    '*.id'         => 'sometimes|numeric',
    '*.percentage' => 'required|numeric',
    '*.title'      => 'required_if:*.id,null|string',
]
```

## Validation Messages

Validation messages are defined in `Resources/i18n/en.php`. Any message can be replaced with a custom
string, or translated to another language. The English strings are always loaded during `Factory`
instantiation.

Depending on the failure type, various variables will be available to use, however, the following are
always available for all messages:

* `:attribute`: the attribute under validation, alias will be used if set,
* `:value`: the value of the attribute under validation, converted to string with arrays and objects as JSON strings.

#### Loading translation messages

By default, only the English messages are loaded by the `Factory` class. At the time of writing a German translation
has been provided by contributors, however any language can be added by creating a PHP file that returns an array
of strings with the message keys and the new messages.

To load a built-in language, you must call `Factor::registerLanguageMessages()` before calling validate. For example:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->registerLanguageMessages('de');
```

`registerLanguageMessages` has a second, optional, argument that allows the path to the language file to be
specified. If not provided, then the library path of `<vendor_dir>/src/Resources/i18n` will be used. If you wish
to use a completely customised language file, then use the second argument to provide your file. This can be
an English language file to fully override the default messages.

For example:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->registerLanguageMessages('en', '/path/to/project/i18n/en_US.php');
```

You can make multiple calls to add multiple languages:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->registerLanguageMessages('en', '/path/to/project/i18n/en_US.php');
$factory->registerLanguageMessages('es', '/path/to/project/i18n/es.php');
$factory->registerLanguageMessages('de', '/path/to/project/i18n/de.php');
```

#### Custom Messages for Validator

All messages are stored in a `MessageBag` on the `Factory` instance. Additional languages can be added to this
message bag, or customised on the specific validation instance. Additionally, the default language can be set
on the message bag on the Factory, or a specific language set on the validation instance.

To add a new set of messages:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->messages()->add('es', [
    'rule.required' => 'Se requiere :attribute',
]);

$validation = $factory->validate($inputs, $rules);
$validation->setLanguage('es')->validate();
```

Or override the default English strings:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->messages()->replace('en', 'rule.required', 'Se requiere :attribute');

$validation = $factory->validate($inputs, $rules);
$validation->validate();
```

Or set the default language:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->messages()->default('es');

$validation = $factory->validate($inputs, $rules);
$validation->validate();
```

#### Custom Message for Specific Attribute Rule

Sometimes you may want to set custom messages for specific attribute rules to make them more
explicit or to add other information. This is done by adding a message key for the attribute
with a `:` and the rule name.

For example:

```php
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validation_a = $validator->make($input, [
	'age' => 'required|min:18'
]);

$validation_a->messages()->add('en', ['age:min'=> '18+ only']);

$validation_a->validate();
```

Sometimes you may wish to use parameters from other rules in your error messages. From version 1.6.0
you can access these using dot notation for the rule name and then the parameter you wish to use. For
example:

A `password` attribute is validated using `required|between:8,16|regex:/^[\\da-zA-Z!$%+.]+$/` but the
error messages want to always reference the min/max values. This can be done as:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->messages()->replace('en', 'password:between', 'Your password must be between :min and :max characters and only [! $ % + .] as special characters.');
$factory->messages()->replace('en', 'password:regex', 'Your password must be between :between.min and :between.max characters and only [! $ % + .] as special characters.');
```

For the `regex` message, the parameters from `between` are referenced by prefixing the min/max with `between.`.
Not all rules have parameters, in these instances there will be no replacement made.

Note that only rule parameters for the same attribute can be referenced. You cannot access parameters
from a completely different attribute e.g.: if you validated email or username, you would not be able
to access those parameters in the password context.

#### Custom Messages for Rules

Some rules have several possible validation messages. These are all named as `rule.<name>.<check>`. To change
the message, override or add the specific message.

For example, `uploaded_file` can have failures for the file, min/max size and type. These are bound to:

* rule.uploaded_file
* rule.uploaded_file.min_size
* rule.uploaded_file.max_size
* rule.uploaded_file.type

To change any of the sub-messages, add/override that message key on the message bag.

For example:

```php
use Somnambulist\Components\Validation\Factory;

$validator = new Factory();
$validation_a = $validator->make($input, [
	'age' => 'required|min:18'
]);

$validation->messages()->add('en', 'age:min', '18+ only');

$validation->validate();
```

> Unlike `rakit`, it is not possible to set custom messages in the `Rule` instances directly.
Any message must be set in the message bag.

### Complex Translation Needs

The system for translations in this library is rather basic. If you have complex needs, or wish to handle
countables etc. Then all error messages are stored as `ErrorMessage` instances containing the message key
and the variables for that message.

Instead of using the `ErrorBag` to display messages, you can use the underlying array (or a `DataBag` instance)
and then pass the message keys to your translation system along with the variables.

Note that errors are a nested set by attribute and rule name.

## Working with Error Messages

Error messages are collected in an `ErrorBag` instance that you can access via `errors()` on the validation
instance.

```php
use Somnambulist\Components\Validation\Factory;

$validation = (new Factory())->validate($inputs, $rules);

$errors = $validation->errors();
```

Now you can use the following methods to retrieve the messages:

#### `all(string $format = ':message')`

Get all messages as a flattened array:

```php
$messages = $errors->all();
// [
//     'email is not a valid email address',
//     'password minimum is 6 characters',
//     'password must contain capital letters'
// ]

$messages = $errors->all('<li>:message</li>');
// [
//     '<li>email is not a valid email address</li>',
//     '<li>password minimum is 6 character</li>',
//     '<li>password must contain capital letters</li>'
// ]
```

#### `firstOfAll(string $format = ':message', bool $dotNotation = false)`

Get only the first message from all existing keys:

```php
$messages = $errors->firstOfAll();
// [
//     'email' => 'Email is not valid email',
//     'password' => 'Password minimum 6 character',
// ]

$messages = $errors->firstOfAll('<li>:message</li>');
// [
//     'email' => '<li>Email is not valid email</li>',
//     'password' => '<li>Password minimum 6 character</li>',
// ]
```

Argument `$dotNotation` is for array validation. If it is `false` it will return the original array structure,
if it is `true` it will return a flattened array with dot notation keys.

For example:

```php
$messages = $errors->firstOfAll(':message', false);
// [
//     'contacts' => [
//          1 => [
//              'email' => 'Email is not valid email',
//              'phone' => 'Phone is not valid phone number'
//          ],
//     ],
// ]

$messages = $errors->firstOfAll(':message', true);
// [
//     'contacts.1.email' => 'Email is not valid email',
//     'contacts.1.phone' => 'Email is not valid phone number',
// ]
```

#### `first(string $key)`

Get the first message for the given key. It will return a `string` if key has any error message, or `null` if the key has no errors.

For example:

```php
if ($emailError = $errors->first('email')) {
    echo $emailError;
}
```

#### `toArray()`

Get the raw underlying associative array of ErrorMessage objects.

For example:

```php
$messages = $errors->toArray();
// [
//     'email' => [
//         'email' => 'Email is not valid email'
//     ],
//     'password' => [
//         'min' => 'Password minimum 6 character',
//         'regex' => Password must contains capital letters'
//     ]
// ]
```

#### `toDataBag()`

Get the raw underlying associative array of ErrorMessage objects as a `DataBag` instance.

For example:

```php
$message = $errors->toDataBag()->filter()->first();
```

#### `count()`

Get the number of error messages.

#### `has(string $key)`

Check if the given key has an error. It returns `true` if a key has an error, and `false` otherwise.

## Register/Override Rules

By default, all built-in rules are registered automatically to the `Factory` instance. Some of these
are required internally (e.g. `required` and `callback`); however you can override or add any number
of new rules to the factory to use for your validations.

This is done by accessing the `addRule()` method on the `Factory` and adding a new rule instance.

For example, you want to create the `unique` validator that will check field availability in a database.

First, lets create `UniqueRule` class:

```php
<?php declare(strict_types=1);

use Somnambulist\Components\Validation\Rule;

class UniqueRule extends Rule
{
    protected string $message = ":attribute :value has been used";
    protected array $fillableParams = ['table', 'column', 'except'];
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->assertHasRequiredParameters(['table', 'column']);

        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');

        if ($except && $except == $value) {
            return true;
        }

        // do query
        $stmt = $this->pdo->prepare(sprintf('select count(*) as count from %s where %s = :value', $table, $column));
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // true for valid, false for invalid
        return intval($data['count']) === 0;
    }
}
```

Now to register this rule it needs adding to the `Factory` instance:

```php
use Somnambulist\Components\Validation\Factory;

$factory = new Factory();
$factory->addRule('unique', new UniqueRule($pdo));
```

Now you can use it like this:

```php
$validation = $factory->validate($_POST, [
    'email' => 'email|unique:users,email,exception@mail.com'
]);
```

In the `UniqueRule` above, the property `$message` is used for the invalid message. The property
`$fillableParams` defines the order and names of the arguments for the rule. By default,
`fillParameters` will fill parameters listed in `$fillableParams` from the string rules.
For example, `unique:users,email,exception@mail.com` in example above, will set:

```php
$params['table'] = 'users';
$params['column'] = 'email';
$params['except'] = 'exception@mail.com';
```

> If you want your custom rule to accept parameter lists like `in`,`not_in`, or `uploaded_file` rules,
you need to override the `fillParameters(array $params)` method in your custom rule class.

Note that the `unique` rule that we created above also can be used like this:

```php
$validation = $factory->validate($_POST, [
    'email' => [
    	'required', 'email',
    	$factory('unique', 'users', 'email')
    ]
]);
```

You can improve `UniqueRule` class above by adding some methods to set the params instead of using
the string format:

```php
<?php

class UniqueRule extends Rule
{
    public function table(string $table): self
    {
        $this->params['table'] = $table;
        
        return $this;
    }

    public function column(string $column): self
    {
        $this->params['column'] = $column;
        
        return $this;
    }

    public function except(string $value): self
    {
        $this->params['except'] = $value;
        
        return $this;
    }
}
```

Now configuring the rule becomes:

```php
$validation = $factory->validate($_POST, [
    'email' => [
    	'required', 'email',
    	$validator('unique')->table('users')->column('email')->except('exception@mail.com')
    ]
]);
```

### Implicit Rule

An implicit rule is a rule that if it's invalid, the next rules will be ignored. For example, if
the attribute didn't pass `required*` rules, the next rules will be invalid. To prevent unnecessary
validation and error messages, we make `required*` rules to be implicit.

To make your custom rule implicit, you can make `$implicit` property value to be `true`. For example:

```php
<?php
use Somnambulist\Components\Validation\Rule;

class YourCustomRule extends Rule
{
    protected bool $implicit = true;
}
```

### Modify Value

In some cases, you may want your custom rule to be able to modify the attribute value like the
`default/defaults` rule. In the current and next rule checks, your modified value will be used.

To do this, you should implement `Somnambulist\Components\Validation\Rules\Contracts\ModifyValue`
and create the method `modifyValue(mixed $value)` on your custom rule class.

For example:

```php
<?php

use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Contracts\ModifyValue;

class YourCustomRule extends Rule implements ModifyValue
{
    public function modifyValue(mixed $value): mixed
    {
        // Do something with $value

        return $value;
    }
}
```

### Before Validation Hook

You may want to do some preparation before running the validation. For example, the
`uploaded_file` rule will resolve the attribute value that comes from `$_FILES`
(undesirable) array structure to be a well-organized array.

To do this, you should implement `Somnambulist\Components\Validation\Rules\Contracts\BeforeValidate`
and create the method `beforeValidate()` on your custom rule class.

For example:

```php
<?php

use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Contracts\BeforeValidate;

class YourCustomRule extends Rule implements BeforeValidate
{
    public function beforeValidate(): void
    {
        // For example change attribute value
        // Current value can be accessed using $this->attribute->value()
        $this->validation->input()->set($this->attribute->key(), 'Your custom value');
    }
}
```

## Tests

PHPUnit 11+ is used for testing. Run tests via `vendor/bin/phpunit`.

## Contributing

Contributions are welcome! Fork the repository and make a PR back. Please ensure that your code is formatted
using PSR-12 coding standards, and all PHP files include `declare(strict_types=1);` on the opening `<?php` tag.
If in doubt about any code-style convention, look at the existing files and follow along.

This library currently targets PHP 8.2+. Note that external libraries should not be added to this project. 

If adding new functionality ensure the `README.md` file is updated with your changes and include appropriate
tests and if possible, language translations with English as the primary requirement.

For bug fixes a failing case must be included in a test. Changes without appropriate tests or that cannot be
replicated may be rejected.
