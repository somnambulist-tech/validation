<?php declare(strict_types=1);


namespace Somnambulist\Components\Validation\Tests\Rules;

use Somnambulist\Components\Validation\Rules\AlphaNum;
use PHPUnit\Framework\TestCase;

class AlphaNumTest extends TestCase
{

    public function setUp(): void
    {
        $this->rule = new AlphaNum;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('123'));
        $this->assertTrue($this->rule->check('abc'));
        $this->assertTrue($this->rule->check('123abc'));
        $this->assertTrue($this->rule->check('abc123'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('foo 123'));
        $this->assertFalse($this->rule->check('123 foo'));
        $this->assertFalse($this->rule->check(' foo123 '));
    }
}
