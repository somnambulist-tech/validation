<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use Somnambulist\Components\Validation\Rules\AlphaSpaces;
use PHPUnit\Framework\TestCase;

class AlphaSpacesTest extends TestCase
{
    public function setUp(): void
    {
        $this->rule = new AlphaSpaces();
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->check('abc'));
        $this->assertTrue($this->rule->check('foo bar'));
        $this->assertTrue($this->rule->check('foo bar  bar'));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->check('123'));
        $this->assertFalse($this->rule->check('123abc'));
        $this->assertFalse($this->rule->check('abc123'));
        $this->assertFalse($this->rule->check('foo_123'));
        $this->assertFalse($this->rule->check('213-foo'));
    }
}
