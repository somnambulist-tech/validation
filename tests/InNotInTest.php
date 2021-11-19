<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

/**
 * Class InNotInTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\InNotInTest
 */
class InNotInTest extends TestCase
{
    protected ?Factory $validator = null;

    protected function setUp(): void
    {
        $this->validator = new Factory;
    }

    public function testRuleInInvalidMessages()
    {
        $validation = $this->validator->validate([
            'number' => 1
        ], [
            'number' => 'in:7,8,9',
        ]);

        $this->assertEquals('number must be one of "7", "8", "9"', $validation->errors()->first('number'));
    }

    public function testRuleNotInInvalidMessages()
    {
        $validation = $this->validator->validate([
            'number' => 1
        ], [
            'number' => 'not_in:1,2,3',
        ]);

        $this->assertEquals('number must not be one of "1", "2", "3"', $validation->errors()->first('number'));
    }
}
