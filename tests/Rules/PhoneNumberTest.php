<?php

declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

/**
 * Class PhoneNumberTest
 *
 * @package    Somnambulist\Components\Validation\Tests\Rules
 * @subpackage Somnambulist\Components\Validation\Tests\Rules\PhoneNumberTest
 */
class PhoneNumberTest extends TestCase
{
    public function testPhone()
    {
        $validator = new Factory();

        $res = $validator->validate(
            [
                'foo' => '+12345678901',
            ],
            [
                'foo' => 'phone',
            ],
        );

        $this->assertTrue($res->passes());

        $res = $validator->validate(
            [
                'foo' => '01234567890',
            ],
            [
                'foo' => 'phone',
            ],
        );

        $this->assertTrue($res->fails());
    }
}
