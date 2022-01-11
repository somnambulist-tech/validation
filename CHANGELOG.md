Change Log
==========

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
