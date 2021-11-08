<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use Somnambulist\Components\Validation\Rules\Numeric;
use PHPUnit\Framework\TestCase;

class NumericTest extends TestCase
{

    public function setUp(): void
    {
        $this->rule = new Numeric;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('123'));
        $this->assertTrue($this->rule->check('123.456'));
        $this->assertTrue($this->rule->check('-123.456'));
        $this->assertTrue($this->rule->check(123));
        $this->assertTrue($this->rule->check(123.456));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foo123'));
        $this->assertFalse($this->rule->check('123foo'));
        $this->assertFalse($this->rule->check([123]));
    }
}
