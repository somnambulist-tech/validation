Change Log
==========

2024-03-02
----------

 * required PHP 8.1+
 * update dependencies
 * fix missing return types

2023-10-05
----------

 * add Turkish translations thanks to [relliv](https://github.com/relliv)

2023-06-05
----------

 * update README to include notes on registering message translations (#22 thanks to [xJuvi](https://github.com/xJuvi) for reporting)

2023-01-23
----------

 * add rule to limit the keys in associative array data: `array_can_only_have_keys`
 * add rule to require all keys be present in associative array data: `array_must_have_keys`
 * fix bug where nested arrays were being incorrect converted to strings

2023-01-12
----------

 * support referencing parameters from other rules for the same attribute for error messages (#14)
 * add documentation for validating array data (#16)
 * add documentation for optional and nullable data
 * add additional methods to `DataBag`
 * add method to `Factory` for loading message resources more easily (suggestion from PR #15)

2022-12-30
----------

 * fix ints being passed to preg_match in alpha_num and alpha_dash rules #13
 * update tests for PHP 8.2 deprecations

2022-12-13
----------

 * add rules introduced via PR #9 to defaults with thanks to [xJuvi](https://github.com/xJuvi)
 * fix language string bug in EndsWith rule / language files thanks to [xJuvi](https://github.com/xJuvi)

2022-11-23
----------

 * add additional string casting to rules where strings are required in function calls

2022-11-22
----------

 * add explicit cast to string on `Regex` rule #11

2022-11-12
----------

 * add `mixed` type hint to `Rule::check` with thanks to [DeveloperMarius](https://github.com/DeveloperMarius)

2022-11-11
----------

 * add `starts_with` and `ends_with` rules with thanks to [DeveloperMarius](https://github.com/DeveloperMarius)
 * some additional code clean up
 * add notes about contributing

2022-11-04
----------

 * better handle multiple file uploads when the attribute key has not been defined using dot notation
 * add additional notes to readme regarding multiple file uploads and requirement for dot notation

2022-09-11
----------

 * add additional tests to check `:` handling in rule attributes

2022-07-26
----------

 * remove unnecessary docblocks

2022-06-20
----------

 * add `length` rule for testing strings of exact length (handles UTF8 strings)

2022-04-19
----------

 * fix PHP 8.0 compatibility calling PHP 8.1 function `array_is_list()` without a fallback

2022-04-04
----------

 * add German translation with thanks to [xJuvi](https://github.com/xJuvi)

2022-03-28
----------

 * add `any_of` and `sometimes` rules
 * remove calls to deprecated mock methods
 * move some test classes to `Rules` namespace

2022-03-23
----------

 * add `rejected` to readme (previously missed)

2022-02-03
----------

 * add `rejected` rule
 * add `number` as alias of `integer` rule

2022-01-11
----------

 * fix strict type check on `Date` rule where value is sometimes null

2021-12-09
----------

 * some additional cleanup from code inspections
 * add `Exists` and `Unique` database rules (uses DBAL)

2021-11-24
----------

 * add readme contents based on rakit/validation readme
 * add setting validation language on Validation
 * clean up imports
 * rename `In`, `NotIn` to use `values()` as the method to set values

2021-11-19
----------

 * add `AttributeBag` and `RuleBag`
 * rename `ValidatorTest` to `FactoryTest`
 * refactor more internals
 * split out tests from `FactoryTest` into smaller groups

2021-11-18
----------

 * add `InputBag` for holding all input items
 * add support for callables in `Helper::arrayGet`
 * rename more methods
 * rename `primaryAttribute` to `parent`
 * remove `setAliases`, `setAlias` is good enough
 * remove `otherAttributes` from attribute, not used anywhere

2021-11-17
----------

 * add `phone`, `uuid`, `prohibited`, `prohibited_if`, `prohibited_unless`, `string`, and `float` rules
 * add `matches` as alias of `regex`
 * add `MimeTypeGuesser` interface to allow injecting alternative guessers in rules
 * add helpers to `In` and `NotIn` for building rules
 * add initial language translation support
 * allow commas in parameters on rules e.g. in, not_in etc.
 * change Rule `key` to `name`
 * move `Interfaces` to `Contracts`
 * move `Traits` to `Behaviours`
 * refactor more internals

2021-11-09
----------

 * remove the humanize keys and override checks - not needed
 * remove unused methods
 * add support for aliases on rules and drop pre-process of input data
 * add support for rules as array of rule -> value as well as [ rule:value ]
 * correct usage of assertEquals in tests

2021-11-04
----------

 * initial commit porting code from https://github.com/rakit/validation
 * clean up some internals
 * make use of PHP8 features
 * rename some classes
