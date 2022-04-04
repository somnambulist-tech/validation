<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\ErrorCollection;
use Somnambulist\Components\Validation\ErrorMessage;

/**
 * Class ErrorBagTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\ErrorBagTest
 */
class ErrorBagTest extends TestCase
{

    public function testCount()
    {
        $errors = new ErrorCollection([
            'email' => [
                'email'  => new ErrorMessage('foo'),
                'unique' => new ErrorMessage('bar'),
            ],
            'age'   => [
                'numeric' => new ErrorMessage('baz'),
                'min'     => new ErrorMessage('qux'),
            ],
        ]);

        $this->assertEquals(4, $errors->count());
    }

    public function testAdd()
    {
        $errors = new ErrorCollection();

        $errors->add('email', 'email', $a = new ErrorMessage('foo'));
        $errors->add('email', 'unique', $b = new ErrorMessage('bar'));
        $errors->add('age', 'numeric', $c = new ErrorMessage('baz'));
        $errors->add('age', 'min', $d = new ErrorMessage('qux'));

        $this->assertEquals([
            'email' => [
                'email'  => $a,
                'unique' => $b,
            ],
            'age'   => [
                'numeric' => $c,
                'min'     => $d,
            ],
        ], $errors->toArray());
    }

    public function testHas()
    {
        $errors = new ErrorCollection([
            'email'              => [
                'email'  => new ErrorMessage('foo'),
                'unique' => new ErrorMessage('bar'),
            ],
            'items.0.id_product' => [
                'numeric' => new ErrorMessage('qwerty'),
            ],
            'items.1.id_product' => [
                'numeric' => new ErrorMessage('qwerty'),
            ],
            'items.2.id_product' => [
                'numeric' => new ErrorMessage('qwerty'),
            ],
        ]);

        $this->assertTrue($errors->has('email'));
        $this->assertTrue($errors->has('email:unique'));
        $this->assertTrue($errors->has('email:email'));
        $this->assertTrue($errors->has('items.0.*'));
        $this->assertTrue($errors->has('items.*.id_product'));
        $this->assertTrue($errors->has('items.0.*:numeric'));
        $this->assertTrue($errors->has('items.*.id_product:numeric'));

        $this->assertFalse($errors->has('not_exists'));
        $this->assertFalse($errors->has('email:unregistered_rule'));
        $this->assertFalse($errors->has('items.3.*'));
        $this->assertFalse($errors->has('items.*.not_exists'));
        $this->assertFalse($errors->has('items.0.*:unregistered_rule'));
    }

    public function testFirst()
    {
        $errors = new ErrorCollection([
            'email'              => [
                'email'  => new ErrorMessage('1'),
                'unique' => new ErrorMessage('2'),
            ],
            'items.0.id_product' => [
                'numeric' => new ErrorMessage('3'),
            ],
            'items.1.id_product' => [
                'numeric' => new ErrorMessage('4'),
            ],
            'items.2.id_product' => [
                'numeric' => new ErrorMessage('5'),
            ],
        ]);

        $this->assertEquals('1', $errors->first('email'));
        $this->assertEquals('1', $errors->first('email:email'));
        $this->assertEquals('2', $errors->first('email:unique'));

        $this->assertEquals('3', $errors->first('items.*'));
        $this->assertEquals('3', $errors->first('items.*.id_product'));
        $this->assertEquals('3', $errors->first('items.0.*'));
        $this->assertEquals('3', $errors->first('items.0.*:numeric'));
        $this->assertEquals('4', $errors->first('items.1.*'));

        $this->assertNull($errors->first('not_exists'));
        $this->assertNull($errors->first('email:unregistered_rule'));
        $this->assertNull($errors->first('items.99.*'));
        $this->assertNull($errors->first('items.*.not_exists'));
        $this->assertNull($errors->first('items.1.id_product:unregistered_rule'));
    }

    public function testGet()
    {
        $errors = new ErrorCollection([
            'email' => [
                'email'  => new ErrorMessage('1'),
                'unique' => new ErrorMessage('2'),
            ],

            'items.0.id_product' => [
                'numeric' => new ErrorMessage('3'),
                'etc'     => new ErrorMessage('x'),
            ],
            'items.0.qty'        => [
                'numeric' => new ErrorMessage('a'),
            ],

            'items.1.id_product' => [
                'numeric' => new ErrorMessage('4'),
                'etc'     => new ErrorMessage('y'),
            ],
            'items.1.qty'        => [
                'numeric' => new ErrorMessage('b'),
            ],
        ]);

        $this->assertEquals([
            'email'  => 'prefix 1 suffix',
            'unique' => 'prefix 2 suffix',
        ], $errors->get('email', 'prefix :message suffix'));

        $this->assertEquals([
            'email' => 'prefix 1 suffix',
        ], $errors->get('email:email', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'numeric' => 'prefix 3 suffix',
                'etc'     => 'prefix x suffix',
            ],
            'items.0.qty'        => [
                'numeric' => 'prefix a suffix',
            ],
            'items.1.id_product' => [
                'numeric' => 'prefix 4 suffix',
                'etc'     => 'prefix y suffix',
            ],
            'items.1.qty'        => [
                'numeric' => 'prefix b suffix',
            ],
        ], $errors->get('items.*', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'numeric' => 'prefix 3 suffix',
                'etc'     => 'prefix x suffix',
            ],
            'items.0.qty'        => [
                'numeric' => 'prefix a suffix',
            ],
        ], $errors->get('items.0.*', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'numeric' => 'prefix 3 suffix',
                'etc'     => 'prefix x suffix',
            ],
            'items.1.id_product' => [
                'numeric' => 'prefix 4 suffix',
                'etc'     => 'prefix y suffix',
            ],
        ], $errors->get('items.*.id_product', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'etc' => 'prefix x suffix',
            ],
            'items.1.id_product' => [
                'etc' => 'prefix y suffix',
            ],
        ], $errors->get('items.*.id_product:etc', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'etc' => 'prefix x suffix',
            ],
            'items.1.id_product' => [
                'etc' => 'prefix y suffix',
            ],
        ], $errors->get('items.*:etc', 'prefix :message suffix'));
    }

    public function testAll()
    {
        $errors = new ErrorCollection([
            'email'              => [
                'email'  => new ErrorMessage('1'),
                'unique' => new ErrorMessage('2'),
            ],
            'items.0.id_product' => [
                'numeric' => new ErrorMessage('3'),
                'etc'     => new ErrorMessage('x'),
            ],
            'items.0.qty'        => [
                'numeric' => new ErrorMessage('a'),
            ],
            'items.1.id_product' => [
                'numeric' => new ErrorMessage('4'),
                'etc'     => new ErrorMessage('y'),
            ],
            'items.1.qty'        => [
                'numeric' => new ErrorMessage('b'),
            ],
        ]);

        $this->assertEquals([
            'prefix 1 suffix',
            'prefix 2 suffix',

            'prefix 3 suffix',
            'prefix x suffix',
            'prefix a suffix',

            'prefix 4 suffix',
            'prefix y suffix',
            'prefix b suffix',
        ], $errors->all('prefix :message suffix'));
    }

    public function testFirstOfAll()
    {
        $errors = new ErrorCollection([
            'email'              => [
                'email'  => new ErrorMessage('1'),
                'unique' => new ErrorMessage('2'),
            ],
            'items.0.id_product' => [
                'numeric' => new ErrorMessage('3'),
                'etc'     => new ErrorMessage('x'),
            ],
            'items.0.qty'        => [
                'numeric' => new ErrorMessage('a'),
            ],
            'items.1.id_product' => [
                'numeric' => new ErrorMessage('4'),
                'etc'     => new ErrorMessage('y'),
            ],
            'items.1.qty'        => [
                'numeric' => new ErrorMessage('b'),
            ],
        ]);

        $this->assertEquals([
            'email' => 'prefix 1 suffix',
            'items' => [
                [
                    'id_product' => 'prefix 3 suffix',
                    'qty'        => 'prefix a suffix',
                ],
                [
                    'id_product' => 'prefix 4 suffix',
                    'qty'        => 'prefix b suffix',
                ],
            ],
        ], $errors->firstOfAll('prefix :message suffix'));
    }

    public function testFirstOfAllDotNotation()
    {
        $errors = new ErrorCollection([
            'email'              => [
                'email'  => new ErrorMessage('1'),
                'unique' => new ErrorMessage('2'),
            ],
            'items.0.id_product' => [
                'numeric' => new ErrorMessage('3'),
                'etc'     => new ErrorMessage('x'),
            ],
            'items.0.qty'        => [
                'numeric' => new ErrorMessage('a'),
            ],
            'items.1.id_product' => [
                'numeric' => new ErrorMessage('4'),
                'etc'     => new ErrorMessage('y'),
            ],
            'items.1.qty'        => [
                'numeric' => new ErrorMessage('b'),
            ],
        ]);

        $this->assertEquals([
            'email'              => 'prefix 1 suffix',
            'items.0.id_product' => 'prefix 3 suffix',
            'items.0.qty'        => 'prefix a suffix',
            'items.1.id_product' => 'prefix 4 suffix',
            'items.1.qty'        => 'prefix b suffix',
        ], $errors->firstOfAll('prefix :message suffix', true));
    }
}
