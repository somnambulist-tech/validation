<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Exceptions\RuleException;
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Tests\Fixtures\Even;
use Somnambulist\Components\Validation\Tests\Fixtures\Required;

use const UPLOAD_ERR_OK;

/**
 * Class BeforeAfterTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\BeforeAfterTest
 */
class BeforeAfterTest extends TestCase
{
    protected ?Factory $validator = null;
    protected function setUp(): void
    {
        $this->validator = new Factory();
    }

    public function testBeforeRule()
    {
        $data = ["date" => (new DateTime())->format('Y-m-d')];
        $validator = $this->validator->make($data, [
            'date' => 'required|before:tomorrow'
        ]);
        $validator->validate();
        $this->assertTrue($validator->passes());
        $validator2 = $this->validator->make($data, [
            'date' => "required|before:last week"
        ]);
        $validator2->validate();
        $this->assertFalse($validator2->passes());
    }

    public function testAfterRule()
    {
        $data = ["date" => (new DateTime())->format('Y-m-d')];
        $validator = $this->validator->make($data, [
            'date' => 'required|after:yesterday'
        ]);
        $validator->validate();
        $this->assertTrue($validator->passes());
        $validator2 = $this->validator->make($data, [
            'date' => "required|after:next year"
        ]);
        $validator2->validate();
        $this->assertFalse($validator2->passes());
    }
}
