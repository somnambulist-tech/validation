<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rules\EndsWith;

class EndsWithTest extends TestCase
{

    public function setUp(): void
    {
        $this->rule = new EndsWith;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters(['Mustermann'])->check('Maximilian Mustermann'));
        $this->assertTrue($this->rule->fillParameters(['0'])->check(6010));
        $this->assertTrue($this->rule->fillParameters(['Mustermann'])->check(['Max', 'Mustermann']));
        $this->assertTrue($this->rule->fillParameters(['Mustermann'])->check(['firstname' => 'Max', 'lastname' => 'Mustermann']));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters(['Mustermann'])->check('Peter Musterfrau'));
        $this->assertFalse($this->rule->fillParameters(['0'])->check(5601));
        $this->assertFalse($this->rule->fillParameters(['Musterfrau'])->check(['Peter', 'Max', 'Mustermann']));
        $this->assertFalse($this->rule->fillParameters(['Musterfrau'])->check(['firstname' => 'Peter', 'lastname' => 'Mustermann']));
    }
}
