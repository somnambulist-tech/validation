<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

use const UPLOAD_ERR_NO_FILE;

/**
 * Class SometimesTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\Rules\SometimesTest
 */
class SometimesTest extends TestCase
{
    protected ?Factory $validator = null;
    protected function setUp(): void
    {
        $this->validator = new Factory();
    }

    public function testIgnoreNextRulesWithNullableRule()
    {
        $file = [
            'name' => 'sample.txt',
            'type' => 'plain/text',
            'tmp_name' => __FILE__,
            'size' => 1000,
            'error' => UPLOAD_ERR_NO_FILE,
        ];
        $data1 = [
            'name' => 'a@b.c'
        ];
        $data2 = [
            'file' => $file,
            'name' => 'a@b.c'
        ];
        $rules = [
            'file' => 'sometimes|required|uploaded_file:0,500K,png,jpeg',
            'name' => 'email'
        ];
        $validation1 = $this->validator->validate($data1, $rules);
        $validation2 = $this->validator->validate($data2, $rules);
        $this->assertTrue($validation1->passes());
        $this->assertFalse($validation2->passes());
    }
}
