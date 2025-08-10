<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\In;

class InTest extends TestCase
{
    private Rule $rule;

    public function setUp(): void
    {
        $this->rule = new In;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([1,2,3])->check(1));
        $this->assertTrue($this->rule->fillParameters(['1', 'bar', '3'])->check('bar'));
        $this->assertTrue($this->rule->fillParameters(['1', 'bar', '3'])->check(['bar', '3']));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([1,2,3])->check(4));
        $this->assertFalse($this->rule->fillParameters([1,2,3])->check([3,4]));
    }

    public function testStricts()
    {
        // Not strict
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(1));
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(true));
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check([true, 3]));

        // Strict
        $this->rule->strict();
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(1));
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check([1,3]));
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
