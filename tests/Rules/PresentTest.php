<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

/**
 * Class PresentTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\Rules\PresentTest
 */
class PresentTest extends TestCase
{
    protected ?Factory $validator = null;

    protected function setUp(): void
    {
        $this->validator = new Factory;
    }

    public function testRulePresent()
    {
        $v1 = $this->validator->validate([
        ], [
            'something' => 'present'
        ]);
        $this->assertFalse($v1->passes());

        $v2 = $this->validator->validate([
            'something' => 10
        ], [
            'something' => 'present'
        ]);
        $this->assertTrue($v2->passes());
    }
}
