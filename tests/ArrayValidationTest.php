<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

class ArrayValidationTest extends TestCase
{
    public function testValidatingAssociativeArrays()
    {
        $validation = (new Factory())->make([
            'cart' => [
                [
                    'qty' => 'xyz',
                    'itemName' => 'Lorem ipsum',
                    'some_other' => 'value',
                ],
                [
                    'qty' => 10,
                    'itemName' => 'Lorem ipsum',
                    'attributes' => [
                        [
                            'name' => 'color',
                            'value' => null
                        ]
                    ]
                ],
            ]
        ], [
            'cart' => 'array|array_can_only_have_keys:itemName,qty,attributes',
            'cart.*.itemName' => 'required',
            'cart.*.qty' => 'required|numeric',
            'cart.*.attributes.*.value' => 'required'
        ]);

        $validation->validate();

        $this->assertTrue($validation->fails());

        $errors = $validation->errors();

        $this->assertEquals('cart must only have the following keys: "itemName", "qty", "attributes"', $errors->first('cart'));
    }

    public function testValidatingAssociativeArraysWithAllKeys()
    {
        $validation = (new Factory())->make([
            'cart' => [
                [
                    'qty' => 'xyz',
                    'itemName' => 'Lorem ipsum',
                ],
                [
                    'qty' => 10,
                    'itemName' => 'Lorem ipsum',
                    'attributes' => [
                        [
                            'name' => 'color',
                            'value' => null
                        ]
                    ]
                ],
            ]
        ], [
            'cart' => 'array|array_must_have_keys:itemName,qty,attributes',
            'cart.*.itemName' => 'string',
            'cart.*.qty' => 'numeric',
            'cart.*.attributes.*.value' => 'required'
        ]);

        $validation->validate();

        $this->assertTrue($validation->fails());

        $errors = $validation->errors();

        $this->assertEquals('cart must specify all of the following keys: "itemName", "qty", "attributes"', $errors->first('cart'));
    }
}
