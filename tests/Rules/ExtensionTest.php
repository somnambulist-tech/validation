<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\Extension;

class ExtensionTest extends TestCase
{
    private Rule $rule;

    public function setUp(): void
    {
        $this->rule = new Extension;
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters(['pdf','png','txt'])->check('somefile.txt'));
        $this->assertTrue($this->rule->fillParameters(['.pdf','.png','.txt'])->check('somefile.txt'));
        $this->assertTrue($this->rule->fillParameters(['pdf','png','txt'])->check('path/to/somefile.txt'));
        $this->assertTrue($this->rule->fillParameters(['pdf','png','txt'])->check('./absolute/path/to/somefile.txt'));
        $this->assertTrue($this->rule->fillParameters(['pdf','png','txt'])->check('https://site.test/somefile.txt'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters(['pdf','png','txt'])->check(''));
        $this->assertFalse($this->rule->fillParameters(['pdf','png','txt'])->check('.dotfile'));
        $this->assertFalse($this->rule->fillParameters(['pdf','png','txt'])->check('notafile'));
        $this->assertFalse($this->rule->fillParameters(['pdf','png','txt'])->check('somefile.php'));
        $this->assertFalse($this->rule->fillParameters(['.pdf','.png','.txt'])->check('somefile.php'));
    }
}
