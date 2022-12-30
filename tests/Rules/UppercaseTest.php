<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Uppercase;

class UppercaseTest extends TestCase
{
    private Rule $rule;

    public function setUp(): void
    {
        $this->rule = new Uppercase;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('USERNAME'));
        $this->assertTrue($this->rule->check('FULL NAME'));
        $this->assertTrue($this->rule->check('FULL_NAME'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('username'));
        $this->assertFalse($this->rule->check('Username'));
        $this->assertFalse($this->rule->check('userName'));
    }
}
