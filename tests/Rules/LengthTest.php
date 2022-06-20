<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use Somnambulist\Components\Validation\Rules\Between;
use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rules\Length;

class LengthTest extends TestCase
{
    private ?Length $rule = null;

    public function setUp(): void
    {
        $this->rule = new Length();
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([6])->check('foobar'));
        $this->assertTrue($this->rule->fillParameters([6])->check('футбол'));
        $this->assertTrue($this->rule->fillParameters([2])->check(23));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([5])->check('foobar'));
        $this->assertFalse($this->rule->fillParameters([5])->check('футбол'));
        $this->assertFalse($this->rule->fillParameters([6])->check(23));
    }
}
