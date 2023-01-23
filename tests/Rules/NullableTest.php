<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

use const UPLOAD_ERR_OK;

class NullableTest extends TestCase
{
    protected ?Factory $validator = null;

    protected function setUp(): void
    {
        $this->validator = new Factory;
    }

    public function testIgnoreNextRulesWithNullableRule()
    {
        $emptyFile = [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];

        $invalidFile = [
            'name' => 'sample.txt',
            'type' => 'plain/text',
            'tmp_name' => __FILE__,
            'size' => 1000,
            'error' => UPLOAD_ERR_OK,
        ];

        $data1 = [
            'file' => $emptyFile,
            'name' => ''
        ];

        $data2 = [
            'file' => $invalidFile,
            'name' => 'a@b.c'
        ];

        $rules = [
            'file' => 'nullable|uploaded_file:0,500K,png,jpeg',
            'name' => 'nullable|email'
        ];

        $validation1 = $this->validator->validate($data1, $rules);
        $validation2 = $this->validator->validate($data2, $rules);

        $this->assertTrue($validation1->passes());
        $this->assertFalse($validation2->passes());
    }
}
