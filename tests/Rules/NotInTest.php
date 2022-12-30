<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\NotIn;

class NotInTest extends TestCase
{
    private Rule $rule;

    public function setUp(): void
    {
        $this->rule = new NotIn;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters(['2', '3', '4'])->check('1'));
        $this->assertTrue($this->rule->fillParameters([1, 2, 3])->check(5));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters(['bar', 'baz', 'qux'])->check('bar'));
    }

    public function testStricts()
    {
        // Not strict
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(1));
        $this->assertFalse($this->rule->fillParameters(['1', '2', '3'])->check(true));

        // Strict
        $this->rule->strict();
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(1));
        $this->assertTrue($this->rule->fillParameters(['1', '2', '3'])->check(1));
    }
}
