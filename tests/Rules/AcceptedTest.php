<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use Somnambulist\Components\Validation\Rules\Accepted;
use PHPUnit\Framework\TestCase;

class AcceptedTest extends TestCase
{
    public function setUp(): void
    {
        $this->rule = new Accepted();
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('yes'));
        $this->assertTrue($this->rule->check('on'));
        $this->assertTrue($this->rule->check('1'));
        $this->assertTrue($this->rule->check(1));
        $this->assertTrue($this->rule->check(true));
        $this->assertTrue($this->rule->check('true'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(''));
        $this->assertFalse($this->rule->check('onn'));
        $this->assertFalse($this->rule->check(' 1'));
        $this->assertFalse($this->rule->check(10));
    }
}
