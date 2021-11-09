<?php declare(strict_types=1);


namespace Somnambulist\Components\Validation\Tests;

use Somnambulist\Components\Validation\ErrorBag;
use PHPUnit\Framework\TestCase;

class ErrorBagTest extends TestCase
{

    public function testCount()
    {
        $errors = new ErrorBag([
            'email' => [
                'email' => 'foo',
                'unique' => 'bar',
            ],
            'age' => [
                'numeric' => 'baz',
                'min' => 'qux'
            ]
        ]);

        $this->assertEquals(4, $errors->count());
    }

    public function testAdd()
    {
        $errors = new ErrorBag();

        $errors->add('email', 'email', 'foo');
        $errors->add('email', 'unique', 'bar');
        $errors->add('age', 'numeric', 'baz');
        $errors->add('age', 'min', 'qux');

        $this->assertEquals([
            'email' => [
                'email' => 'foo',
                'unique' => 'bar',
            ],
            'age' => [
                'numeric' => 'baz',
                'min' => 'qux'
            ]
        ], $errors->toArray());
    }

    public function testHas()
    {
        $errors = new ErrorBag([
            'email' => [
                'email' => 'foo',
                'unique' => 'bar',
            ],
            'items.0.id_product' => [
                'numeric' => 'qwerty'
            ],
            'items.1.id_product' => [
                'numeric' => 'qwerty'
            ],
            'items.2.id_product' => [
                'numeric' => 'qwerty'
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
        $errors = new ErrorBag([
            'email' => [
                'email' => '1',
                'unique' => '2',
            ],
            'items.0.id_product' => [
                'numeric' => '3'
            ],
            'items.1.id_product' => [
                'numeric' => '4'
            ],
            'items.2.id_product' => [
                'numeric' => '5'
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
        $errors = new ErrorBag([
            'email' => [
                'email' => '1',
                'unique' => '2',
            ],

            'items.0.id_product' => [
                'numeric' => '3',
                'etc' => 'x'
            ],
            'items.0.qty' => [
                'numeric' => 'a'
            ],

            'items.1.id_product' => [
                'numeric' => '4',
                'etc' => 'y'
            ],
            'items.1.qty' => [
                'numeric' => 'b'
            ]
        ]);

        $this->assertEquals([
            'email' => 'prefix 1 suffix',
            'unique' => 'prefix 2 suffix'
        ], $errors->get('email', 'prefix :message suffix'));

        $this->assertEquals([
            'email' => 'prefix 1 suffix',
        ], $errors->get('email:email', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'numeric' => 'prefix 3 suffix',
                'etc' => 'prefix x suffix',
            ],
            'items.0.qty' => [
                'numeric' => 'prefix a suffix',
            ],
            'items.1.id_product' => [
                'numeric' => 'prefix 4 suffix',
                'etc' => 'prefix y suffix',
            ],
            'items.1.qty' => [
                'numeric' => 'prefix b suffix',
            ]
        ], $errors->get('items.*', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'numeric' => 'prefix 3 suffix',
                'etc' => 'prefix x suffix'
            ],
            'items.0.qty' => [
                'numeric' => 'prefix a suffix',
            ]
        ], $errors->get('items.0.*', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'numeric' => 'prefix 3 suffix',
                'etc' => 'prefix x suffix'
            ],
            'items.1.id_product' => [
                'numeric' => 'prefix 4 suffix',
                'etc' => 'prefix y suffix'
            ]
        ], $errors->get('items.*.id_product', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'etc' => 'prefix x suffix'
            ],
            'items.1.id_product' => [
                'etc' => 'prefix y suffix'
            ]
        ], $errors->get('items.*.id_product:etc', 'prefix :message suffix'));

        $this->assertEquals([
            'items.0.id_product' => [
                'etc' => 'prefix x suffix'
            ],
            'items.1.id_product' => [
                'etc' => 'prefix y suffix'
            ]
        ], $errors->get('items.*:etc', 'prefix :message suffix'));
    }

    public function testAll()
    {
        $errors = new ErrorBag([
            'email' => [
                'email' => '1',
                'unique' => '2',
            ],
            'items.0.id_product' => [
                'numeric' => '3',
                'etc' => 'x'
            ],
            'items.0.qty' => [
                'numeric' => 'a'
            ],
            'items.1.id_product' => [
                'numeric' => '4',
                'etc' => 'y'
            ],
            'items.1.qty' => [
                'numeric' => 'b'
            ]
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
        $errors = new ErrorBag([
            'email' => [
                'email' => '1',
                'unique' => '2',
            ],
            'items.0.id_product' => [
                'numeric' => '3',
                'etc' => 'x'
            ],
            'items.0.qty' => [
                'numeric' => 'a'
            ],
            'items.1.id_product' => [
                'numeric' => '4',
                'etc' => 'y'
            ],
            'items.1.qty' => [
                'numeric' => 'b'
            ]
        ]);

        $this->assertEquals([
            'email' => 'prefix 1 suffix',
            'items' => [
                [
                    'id_product' => 'prefix 3 suffix',
                    'qty' => 'prefix a suffix'
                ],
                [
                    'id_product' => 'prefix 4 suffix',
                    'qty' => 'prefix b suffix'
                ],
            ]
        ], $errors->firstOfAll('prefix :message suffix'));
    }

    public function testFirstOfAllDotNotation()
    {
        $errors = new ErrorBag([
            'email' => [
                'email' => '1',
                'unique' => '2',
            ],
            'items.0.id_product' => [
                'numeric' => '3',
                'etc' => 'x'
            ],
            'items.0.qty' => [
                'numeric' => 'a'
            ],
            'items.1.id_product' => [
                'numeric' => '4',
                'etc' => 'y'
            ],
            'items.1.qty' => [
                'numeric' => 'b'
            ]
        ]);

        $this->assertEquals([
            'email' => 'prefix 1 suffix',
            'items.0.id_product' => 'prefix 3 suffix',
            'items.0.qty' => 'prefix a suffix',
            'items.1.id_product' => 'prefix 4 suffix',
            'items.1.qty' => 'prefix b suffix',
        ], $errors->firstOfAll('prefix :message suffix', true));
    }
}
