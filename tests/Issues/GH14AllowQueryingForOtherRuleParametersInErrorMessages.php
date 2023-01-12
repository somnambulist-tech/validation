<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Issues;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

class GH14AllowQueryingForOtherRuleParametersInErrorMessages extends TestCase
{
    /**
     * @link https://github.com/somnambulist-tech/validation/issues/14
     */
    public function testForUndeclaredParamsWithRegex()
    {
        $factory = new Factory();
        $factory->messages()->replace('en', 'password:between', 'Your password must have between :min and :max characters and only [! $ % + .] as special characters.');
        $factory->messages()->replace('en', 'password:regex', 'Your password must have between :between.min and :between.max characters and only [! $ % + .] as special characters.');

        $validation = $factory->validate(
            ['password' => 'foobarbaz^'],
            ['password' => 'required|between:8,16|regex:/^[\\da-zA-Z!$%+.]+$/']
        );

        $this->assertEquals(
            'Your password must have between 8 and 16 characters and only [! $ % + .] as special characters.',
            $validation->errors()->first('password')
        );
    }
}
