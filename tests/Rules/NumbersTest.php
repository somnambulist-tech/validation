<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

/**
 * Class NumbersTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\Rules\NumbersTest
 */
class NumbersTest extends TestCase
{
    protected ?Factory $validator = null;
    protected function setUp(): void
    {
        $this->validator = new Factory();
    }

    public function testNumericStringSizeWithoutNumericRule()
    {
        $validation = $this->validator->validate([
            'number' => '1.2345'
        ], [
            'number' => 'max:2',
        ]);
        $this->assertFalse($validation->passes());
    }

    public function testNumericStringSizeWithNumericRule()
    {
        $validation = $this->validator->validate([
            'number' => '1.2345'
        ], [
            'number' => 'numeric|max:2',
        ]);
        $this->assertTrue($validation->passes());
    }
}
