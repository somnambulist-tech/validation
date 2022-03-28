<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Rules\AnyOf;

class AnyOfTest extends TestCase
{

    public function setUp(): void
    {
        $this->rule = new AnyOf();
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([1,2,3])->check('1,2'));
        $this->assertTrue($this->rule->fillParameters(['1', 'bar', '3'])->check('bar'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([1,2,3])->check('1,4'));
    }

    public function testStricts()
    {
        // Not strict
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(1));
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(true));

        // Strict
        $this->rule->strict();
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(1));
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(1));
    }

    public function testWithCommasInStrings()
    {
        $validator = new Factory();

        $res = $validator->validate(
            ['foo' => 'there\'s something'],
            ['foo' => 'in:"there\'s something",that,another,"value\'s here"']
        );

        $this->assertTrue($res->passes());
    }
}
