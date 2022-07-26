<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rules\Callback;

class CallbackTest extends TestCase
{

    public function setUp(): void
    {
        $this->rule = new Callback;
        $this->rule->through(function ($value) {
            return (is_numeric($value) and $value % 2 === 0);
        });
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check(2));
        $this->assertTrue($this->rule->check('4'));
        $this->assertTrue($this->rule->check("1000"));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check(1));
        $this->assertFalse($this->rule->check('abc12'));
        $this->assertFalse($this->rule->check("12abc"));
    }
}
