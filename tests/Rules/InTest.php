<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use Somnambulist\Components\Validation\Rules\In;
use PHPUnit\Framework\TestCase;

class InTest extends TestCase
{

    public function setUp(): void
    {
        $this->rule = new In;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([1,2,3])->check(1));
        $this->assertTrue($this->rule->fillParameters(['1', 'bar', '3'])->check('bar'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([1,2,3])->check(4));
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
}
