<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Helper;

class HelperTest extends TestCase
{

    public function testArrayHas()
    {
        $array = [
            'foo' => [
                'bar' => [
                    'baz' => null
                ]
            ],
            'one.two.three' => null
        ];

        $this->assertTrue(Helper::arrayHas($array, 'foo'));
        $this->assertTrue(Helper::arrayHas($array, 'foo.bar'));
        $this->assertTrue(Helper::arrayHas($array, 'foo.bar.baz'));
        $this->assertTrue(Helper::arrayHas($array, 'one.two.three'));
        $this->assertTrue(Helper::arrayHas($array, 'foo/bar/baz', '/'));

        $this->assertFalse(Helper::arrayHas($array, 'foo.baz'));
        $this->assertFalse(Helper::arrayHas($array, 'bar.baz'));
        $this->assertFalse(Helper::arrayHas($array, 'foo.bar.qux'));
        $this->assertFalse(Helper::arrayHas($array, 'one.two'));
        $this->assertFalse(Helper::arrayHas($array, 'one/two', '/'));
    }

    public function testArrayGet()
    {
        $array = [
            'foo' => [
                'bar' => [
                    'baz' => 'abc'
                ]
            ],
            'one.two.three' => 123
        ];

        $this->assertEquals(Helper::arrayGet($array, 'foo'), $array['foo']);
        $this->assertEquals(Helper::arrayGet($array, 'foo.bar'), $array['foo']['bar']);
        $this->assertEquals(Helper::arrayGet($array, 'foo.bar.baz'), $array['foo']['bar']['baz']);
        $this->assertEquals(123, Helper::arrayGet($array, 'one.two.three'));

        $this->assertNull(Helper::arrayGet($array, 'foo.bar.baz.qux'));
        $this->assertNull(Helper::arrayGet($array, 'one.two'));

        $this->assertEquals(Helper::arrayGet($array, 'foo', delimiter: '/'), $array['foo']);
        $this->assertEquals(Helper::arrayGet($array, 'foo/bar', delimiter: '/'), $array['foo']['bar']);
        $this->assertEquals(123, Helper::arrayGet($array, 'one.two.three', delimiter: '/'));
    }

    public function testArrayDot()
    {
        $array = [
            'foo' => [
                'bar' => [
                    'baz' => 123,
                    'qux' => 456
                ]
            ],
            'comments' => [
                ['id' => 1, 'text' => 'foo'],
                ['id' => 2, 'text' => 'bar'],
                ['id' => 3, 'text' => 'baz'],
            ],
            'one.two.three' => 789
        ];

        $this->assertEquals([
            'foo.bar.baz' => 123,
            'foo.bar.qux' => 456,
            'comments.0.id' => 1,
            'comments.0.text' => 'foo',
            'comments.1.id' => 2,
            'comments.1.text' => 'bar',
            'comments.2.id' => 3,
            'comments.2.text' => 'baz',
            'one.two.three' => 789
        ], Helper::arrayDot($array));

        $this->assertEquals([
            'foo/bar/baz' => 123,
            'foo/bar/qux' => 456,
            'comments/0/id' => 1,
            'comments/0/text' => 'foo',
            'comments/1/id' => 2,
            'comments/1/text' => 'bar',
            'comments/2/id' => 3,
            'comments/2/text' => 'baz',
            'one.two.three' => 789
        ], Helper::arrayDot($array, glue: '/'));
    }

    public function testArraySet()
    {
        $array = [
            'comments' => [
                ['text' => 'foo'],
                ['id' => 2, 'text' => 'bar'],
                ['id' => 3, 'text' => 'baz'],
                ['bar' => 'jax'],
            ]
        ];

        Helper::arraySet($array, 'comments.*.id', null, false);
        Helper::arraySet($array, 'comments.*.x.y', 1, false);
        Helper::arraySet($array, 'comments/*/bar', 'flux', true, '/');

        $this->assertEquals([
            'comments' => [
                ['id' => null, 'text' => 'foo', 'x' => ['y' => 1], 'bar' => 'flux'],
                ['id' => 2, 'text' => 'bar', 'x' => ['y' => 1], 'bar' => 'flux'],
                ['id' => 3, 'text' => 'baz', 'x' => ['y' => 1], 'bar' => 'flux'],
                ['id' => null, 'x' => ['y' => 1], 'bar' => 'flux'],
            ]
        ], $array);
    }

    public function testArrayUnset()
    {
        $array = [
            'users' => [
                'one' => 'user_one',
                'two' => 'user_two',
            ],
            'stuffs' => [1, 'two', ['three'], null, false, true],
            'message' => "lorem ipsum",
        ];

        Helper::arrayUnset($array, 'users.one');
        $this->assertEquals([
            'users' => [
                'two' => 'user_two',
            ],
            'stuffs' => [1, 'two', ['three'], null, false, true],
            'message' => "lorem ipsum",
        ], $array);

        Helper::arrayUnset($array, 'stuffs.*');
        $this->assertEquals([
            'users' => [
                'two' => 'user_two',
            ],
            'stuffs' => [],
            'message' => "lorem ipsum",
        ], $array);

        Helper::arrayUnset($array, 'users/*', '/');
        $this->assertEquals([
            'users' => [],
            'stuffs' => [],
            'message' => "lorem ipsum",
        ], $array);
    }

    public function testJoin()
    {
        $pieces0 = [];
        $pieces1 = [1];
        $pieces2 = [1, 2];
        $pieces3 = [1, 2, 3];

        $separator = ', ';
        $lastSeparator = ', and ';

        $this->assertEquals('', Helper::join($pieces0, $separator, $lastSeparator));
        $this->assertEquals('1', Helper::join($pieces1, $separator, $lastSeparator));
        $this->assertEquals('1, and 2', Helper::join($pieces2, $separator, $lastSeparator));
        $this->assertEquals('1, 2, and 3', Helper::join($pieces3, $separator, $lastSeparator));
    }

    public function testWraps()
    {
        $inputs = [1, 2, 3];

        $this->assertEquals(['-1-', '-2-', '-3-'], Helper::wraps($inputs, '-'));
        $this->assertEquals(['-1+', '-2+', '-3+'], Helper::wraps($inputs, '-', '+'));
    }
}
