<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Date;

class DateTest extends TestCase
{
    private Rule $rule;

    public function setUp(): void
    {
        $this->rule = new Date;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check("2010-10-10"));
        $this->assertTrue($this->rule->fillParameters(['d-m-Y'])->check("10-10-2010"));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check("10-10-2010"));
        $this->assertFalse($this->rule->fillParameters(['Y-m-d'])->check("2010-10-10 10:10"));
    }

    public function testNull()
    {
        $this->assertFalse($this->rule->check(null));
        $this->assertFalse($this->rule->check(''));
    }
}
