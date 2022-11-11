<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rules\StartsWith;

class StartsWithTest extends TestCase
{
    public function setUp(): void
    {
        $this->rule = new StartsWith;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters(['Max'])->check('Maximilian Mustermann'));
        $this->assertTrue($this->rule->fillParameters(['6'])->check(600));
        $this->assertTrue($this->rule->fillParameters(['Max'])->check(['Max', 'Mustermann']));
        $this->assertTrue($this->rule->fillParameters(['Max'])->check(['firstname' => 'Max', 'lastname' => 'Mustermann']));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters(['Max'])->check('Peter Mustermann'));
        $this->assertFalse($this->rule->fillParameters(['6'])->check(5600));
        $this->assertFalse($this->rule->fillParameters(['Max'])->check(['Peter', 'Max', 'Mustermann']));
        $this->assertFalse($this->rule->fillParameters(['Max'])->check(['firstname' => 'Peter', 'lastname' => 'Mustermann']));
    }
}
