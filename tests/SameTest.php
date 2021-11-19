<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

/**
 * Class SameTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\SameTest
 */
class SameTest extends TestCase
{
    protected ?Factory $validator = null;

    protected function setUp(): void
    {
        $this->validator = new Factory;
    }

    public function testSameRuleOnArrayAttribute()
    {
        $validation = $this->validator->validate([
            'users' => [
                [
                    'password' => 'foo',
                    'password_confirmation' => 'foo'
                ],
                [
                    'password' => 'foo',
                    'password_confirmation' => 'bar'
                ],
            ]
        ], [
            'users.*.password_confirmation' => 'required|same:users.*.password',
        ]);

        $this->assertFalse($validation->passes());

        $errors = $validation->errors();
        $this->assertNull($errors->first('users.0.password_confirmation:same'));
        $this->assertNotNull($errors->first('users.1.password_confirmation:same'));
    }
}
