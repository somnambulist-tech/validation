<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Rules\ArrayCanOnlyHaveKeys;

class ArrayHasKeysTest extends TestCase
{
    private Rule $rule;

    public function setUp(): void
    {
        $this->rule = new ArrayCanOnlyHaveKeys();
    }

    public function testValids()
    {
        $this->assertTrue($this->rule->fillParameters([['foo', 'bar', 'baz']])->check(['foo' => 'bar', 'baz' => 'bob']));
    }

    public function testInvalids()
    {
        $this->assertFalse($this->rule->fillParameters([['foo', 'bar', 'baz']])->check(['foo' => 'bar', 'bob' => 'baz']));
        $this->assertFalse($this->rule->fillParameters([['foo', 'bar', 'baz']])->check('foo'));
    }
}
