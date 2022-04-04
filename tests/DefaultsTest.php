<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

/**
 * Class DefaultsTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\DefaultsTest
 */
class DefaultsTest extends TestCase
{
    protected ?Factory $validator = null;
    protected function setUp(): void
    {
        $this->validator = new Factory();
    }

    public function testUsingDefaults()
    {
        $validation = $this->validator->validate([
            'is_active' => null,
            'is_published' => 'invalid-value'
        ], [
            'is_active' => 'defaults:0|required|in:0,1',
            'is_enabled' => 'defaults:1|required|in:0,1',
            'is_published' => 'required|in:0,1'
        ]);
        $this->assertFalse($validation->passes());
        $errors = $validation->errors();
        $this->assertNull($errors->first('is_active'));
        $this->assertNull($errors->first('is_enabled'));
        $this->assertNotNull($errors->first('is_published'));
// Getting (all) validated data
        $validatedData = $validation->getValidatedData();
        $this->assertEquals([
            'is_active' => '0',
            'is_enabled' => '1',
            'is_published' => 'invalid-value'
        ], $validatedData);
// Getting only valid data
        $validData = $validation->getValidData();
        $this->assertEquals([
            'is_active' => '0',
            'is_enabled' => '1'
        ], $validData);
// Getting only invalid data
        $invalidData = $validation->getInvalidData();
        $this->assertEquals([
            'is_published' => 'invalid-value',
        ], $invalidData);
    }
}
