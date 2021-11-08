<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use Somnambulist\Components\Validation\Rules\Defaults;
use PHPUnit\Framework\TestCase;

class DefaultsTest extends TestCase
{
    public function setUp(): void
    {
        $this->rule = new Defaults;
    }

    public function testDefaults()
    {
        $this->assertTrue($this->rule->fillParameters([10])->check(0));
        $this->assertTrue($this->rule->fillParameters(['something'])->check(null));
        $this->assertTrue($this->rule->fillParameters([[1,2,3]])->check(false));
        $this->assertTrue($this->rule->fillParameters([[1,2,3]])->check([]));
    }
}
