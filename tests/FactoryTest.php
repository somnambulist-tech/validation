<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use Closure;
use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Exceptions\RuleException;
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\MessageBag;
use Somnambulist\Components\Validation\Tests\Fixtures\Even;
use Somnambulist\Components\Validation\Tests\Fixtures\Required;

use const UPLOAD_ERR_OK;

class FactoryTest extends TestCase
{
    protected ?Factory $validator = null;

    protected function setUp(): void
    {
        $this->validator = new Factory;
    }

    public function testPasses()
    {
        $validation = $this->validator->validate([
            'email' => 'emsifa@gmail.com'
        ], [
            'email' => 'required|email'
        ]);

        $this->assertTrue($validation->passes());

        $validation = $this->validator->validate([], [
            'email' => 'required|email'
        ]);

        $this->assertFalse($validation->passes());
    }

    public function testFails()
    {
        $validation = $this->validator->validate([
            'email' => 'emsifa@gmail.com'
        ], [
            'email' => 'required|email'
        ]);

        $this->assertFalse($validation->fails());

        $validation = $this->validator->validate([], [
            'email' => 'required|email'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testSkipEmptyRule()
    {
        $validation = $this->validator->validate([
            'email' => 'emsifa@gmail.com'
        ], [
            'email' => [
                null,
                'email'
            ]
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testNonExistentValidationRule()
    {
        $this->expectException(RuleException::class);

        $validation = $this->validator->make([
            'name' => "some name"
        ], [
            'name' => 'required|xxx'
        ]);

        $validation->validate();
    }

    public function testNewValidationRuleCanBeAdded()
    {
        $this->validator->addRule('even', new Even());

        $data = [4, 6, 8, 10 ];

        $validation = $this->validator->make($data, ['s' => 'even'], []);

        $validation->validate();

        $this->assertTrue($validation->passes());
    }

    public function testInternalValidationRuleCanBeOverridden()
    {
        $this->validator->addRule('required', new Required());

        $data = ['s' => json_encode(['name' => 'space x', 'human' => false])];

        $validation = $this->validator->make($data, ['s' => 'required']);

        $validation->validate();

        $this->assertTrue($validation->passes());
    }

    public function testIgnoreNextRulesWhenImplicitRulesFails()
    {
        $validation = $this->validator->validate([
            'some_value' => 1
        ], [
            'required_field' => 'required|numeric|min:6',
            'required_if_field' => 'required_if:some_value,1|numeric|min:6',
            'must_present_field' => 'present|numeric|min:6',
            'must_accepted_field' => 'accepted|numeric|min:6'
        ]);

        $errors = $validation->errors();

        $this->assertEquals(4, $errors->count());

        $this->assertNotNull($errors->first('required_field:required'));
        $this->assertNull($errors->first('required_field:numeric'));
        $this->assertNull($errors->first('required_field:min'));

        $this->assertNotNull($errors->first('required_if_field:required_if'));
        $this->assertNull($errors->first('required_if_field:numeric'));
        $this->assertNull($errors->first('required_if_field:min'));

        $this->assertNotNull($errors->first('must_present_field:present'));
        $this->assertNull($errors->first('must_present_field:numeric'));
        $this->assertNull($errors->first('must_present_field:min'));

        $this->assertNotNull($errors->first('must_accepted_field:accepted'));
        $this->assertNull($errors->first('must_accepted_field:numeric'));
        $this->assertNull($errors->first('must_accepted_field:min'));
    }

    public function testNextRulesAppliedWhenEmptyValueWithPresent()
    {
        $validation = $this->validator->validate([
            'must_present_field' => '',
        ], [
            'must_present_field' => 'present|array',
        ]);

        $errors = $validation->errors();

        $this->assertEquals(1, $errors->count());

        $this->assertNull($errors->first('must_present_field:present'));
        $this->assertNotNull($errors->first('must_present_field:array'));
    }

    public function testIgnoreOtherRulesWhenAttributeIsNotRequired()
    {
        $validation = $this->validator->validate([
            'an_empty_file' => [
                'name' => '',
                'type' => '',
                'size' => '',
                'tmp_name' => '',
                'error' => UPLOAD_ERR_NO_FILE,
            ],
            'required_if_field' => null,
        ], [
            'optional_field' => 'ipv4|in:127.0.0.1',
            'required_if_field' => 'required_if:some_value,1|email',
            'an_empty_file' => 'uploaded_file'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testDontIgnoreOtherRulesWhenValueIsNotEmpty()
    {
        $validation = $this->validator->validate([
            'an_error_file' => [
                'name' => 'foo',
                'type' => 'text/plain',
                'size' => 10000,
                'tmp_name' => '/tmp/foo',
                'error' => UPLOAD_ERR_CANT_WRITE
            ],
            'optional_field' => 'invalid ip address',
            'required_if_field' => 'invalid email',
        ], [
            'an_error_file' => 'uploaded_file',
            'optional_field' => 'ipv4|in:127.0.0.1',
            'required_if_field' => 'required_if:some_value,1|email'
        ]);

        $this->assertEquals(4, $validation->errors()->count());
    }

    public function testDontIgnoreOtherRulesWhenAttributeIsRequired()
    {
        $validation = $this->validator->validate([
            'optional_field' => 'have a value',
            'required_if_field' => 'invalid email',
            'some_value' => 1
        ], [
            'optional_field' => 'required|ipv4|in:127.0.0.1',
            'required_if_field' => 'required_if:some_value,1|email'
        ]);

        $errors = $validation->errors();

        $this->assertEquals(3, $errors->count());
        $this->assertNotNull($errors->first('optional_field:ipv4'));
        $this->assertNotNull($errors->first('optional_field:in'));
        $this->assertNotNull($errors->first('required_if_field:email'));
    }

    public function testRegisterRulesUsingInvokes()
    {
        $validator = $this->validator;
        $validation = $this->validator->validate([
            'a_field' => null,
            'a_number' => 1000,
            'a_same_number' => 1000,
            'a_date' => '2016-12-06',
            'a_file' => [
                'name' => 'foo',
                'type' => 'text/plain',
                'size' => 10000,
                'tmp_name' => '/tmp/foo',
                'error' => UPLOAD_ERR_OK
            ]
        ], [
            'a_field' => [
                $validator('required'),
            ],
            'a_number' => [
                $validator('min', 2000),
                $validator('max', 5),
                $validator('between', 1, 5),
                $validator('in', [1, 2, 3, 4, 5]),
                $validator('not_in', [1000, 2, 3, 4, 5]),
                $validator('same', 'a_date'),
                $validator('different', 'a_same_number'),
            ],
            'a_date' => [
                $validator('date', 'd-m-Y')
            ],
            'a_file' => [
                $validator('uploaded_file', 20000)
            ]
        ]);

        $errors = $validation->errors();
        $this->assertCount(10, $errors);
    }

    public function testArrayAssocValidation()
    {
        $validation = $this->validator->validate([
            'user' => [
                'email' => 'invalid email',
                'name' => 'John Doe',
                'age' => 16
            ]
        ], [
            'user.email' => 'required|email',
            'user.name' => 'required',
            'user.age' => 'required|min:18'
        ]);

        $errors = $validation->errors();

        $this->assertEquals(2, $errors->count());

        $this->assertNotNull($errors->first('user.email:email'));
        $this->assertNotNull($errors->first('user.age:min'));
        $this->assertNull($errors->first('user.name:required'));
    }

    public function testEmptyArrayAssocValidation()
    {
        $validation = $this->validator->validate([], [
            'user'=> 'required',
            'user.email' => 'email',
        ]);

        $this->assertFalse($validation->passes());
    }

    /**
     * Test root asterisk validation.
     *
     * @dataProvider rootAsteriskProvider
     */
    public function testRootAsteriskValidation(array $data, array $rules, $errors = null)
    {
        $validation = $this->validator->validate($data, $rules);
        $this->assertSame(empty($errors), $validation->passes());
        $errorBag = $validation->errors();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $field = $error[0];
                $rule = $error[1] ?? null;
                $error = $errorBag->get($field);
                $this->assertNotEmpty($error);
                if ($rule !== null) {
                    $this->assertArrayHasKey($rule, $error);
                }
            }
        }
    }

    public function rootAsteriskProvider(): array
    {
        return [
            'control sample success' => [
                ['Body' => ['a' => 1, 'b' => 2]],
                ['Body.*' => 'integer|min:0'],
            ],
            'control sample failure' => [
                ['Body' => ['a' => 1, 'b' => -2]],
                ['Body.*' => 'integer|min:0'],
                [['Body.b', 'min']],
            ],
            'root field success' => [
                ['a' => 1, 'b' => 2],
                ['*' => 'integer|min:0'],
            ],
            'root field failure' => [
                ['a' => 1, 'b' => -2],
                ['*' => 'integer|min:0'],
                [['b', 'min']],
            ],
            'root array success' => [
                [[1], [2]],
                ['*.*' => 'integer|min:0'],
            ],
            'root array failure' => [
                [[1], [-2]],
                ['*.*' => 'integer|min:0'],
                [['1.0', 'min']],
            ],
            'root dict success' => [
                ['a' => ['c' => 1, 'd' => 4], 'b' => ['c' => 'e', 'd' => 8]],
                ['*.c' => 'required'],
            ],
            'root dict failure' => [
                ['a' => ['c' => 1, 'd' => 4], 'b' => ['d' => 8]],
                ['*.c' => 'required'],
                [['b.c', 'required']],
            ],
        ];
    }

    public function testArrayValidation()
    {
        $validation = $this->validator->validate([
            'cart_items' => [
                ['id_product' => 1, 'qty' => 10],
                ['id_product' => null, 'qty' => 10],
                ['id_product' => 3, 'qty' => null],
                ['id_product' => 4, 'qty' => 'foo'],
                ['id_product' => 'foo', 'qty' => 10],
            ]
        ], [
            'cart_items.*.id_product' => 'required|numeric',
            'cart_items.*.qty' => 'required|numeric'
        ]);

        $errors = $validation->errors();

        $this->assertEquals(4, $errors->count());

        $this->assertNotNull($errors->first('cart_items.1.id_product:required'));
        $this->assertNotNull($errors->first('cart_items.2.qty:required'));
        $this->assertNotNull($errors->first('cart_items.3.qty:numeric'));
        $this->assertNotNull($errors->first('cart_items.4.id_product:numeric'));
    }

    public function testArrayValidationWithAliases()
    {
        $validation = $this->validator->validate([
            'cart_items' => [
                ['id_product' => 1, 'qty' => 10],
                ['id_product' => null, 'qty' => 10],
                ['id_product' => 3, 'qty' => null],
                ['id_product' => 4, 'qty' => 'foo'],
                ['id_product' => 'foo', 'qty' => 10],
            ]
        ], [
            'cart_items.*.id_product:Product ID' => 'required|numeric',
            'cart_items.*.qty:Quantity' => 'required|numeric'
        ]);

        $errors = $validation->errors();

        $this->assertEquals(4, $errors->count());

        $this->assertNotNull($errors->first('cart_items.1.id_product:required'));
        $this->assertNotNull($errors->first('cart_items.2.qty:required'));
        $this->assertNotNull($errors->first('cart_items.3.qty:numeric'));
        $this->assertNotNull($errors->first('cart_items.4.id_product:numeric'));

        $this->assertEquals('Product ID is required', $errors->all()[0]);
        $this->assertEquals('Product ID must be numeric', $errors->all()[1]);
    }

    public function testPreservesKeyInArrayValidation()
    {
        $validation = $this->validator->validate([
            'cart' => [
                [
                    'qty' => 'xyz',
                ],
            ]
        ], [
            'cart.*.itemName' => 'required',
            'cart.*.qty' => 'required|numeric'
        ]);

        $errors = $validation->errors();

        $this->assertEquals('cart.0.qty must be numeric', $errors->first('cart.*.qty'));
        $this->assertEquals('cart.0.itemName is required', $errors->first('cart.*.itemName'));
    }

    public function testSetCustomMessagesInValidator()
    {
        $this->validator->messages()->add('en', [
            'rule.required' => 'foo',
            'rule.email' => 'bar',
            'comments.*.text' => 'baz',
            'rule.numeric' => 'baz'
        ]);

        $validation = $this->validator->validate([
            'foo' => null,
            'email' => 'invalid email',
            'something' => 'not numeric',
            'comments' => [
                ['id' => 4, 'text' => ''],
                ['id' => 5, 'text' => 'foo'],
            ]
        ], [
            'foo' => 'required',
            'email' => 'email',
            'something' => 'numeric',
            'comments.*.text' => 'required'
        ]);

        $errors = $validation->errors();
        $this->assertEquals('foo', $errors->first('foo:required'));
        $this->assertEquals('bar', $errors->first('email:email'));
        $this->assertEquals('baz', $errors->first('something:numeric'));
        $this->assertEquals('baz', $errors->first('comments.0.text:required'));
    }

    public function testSetCustomMessagesInValidation()
    {
        $validation = $this->validator->make([
            'foo' => null,
            'email' => 'invalid email',
            'something' => 'not numeric',
            'comments' => [
                ['id' => 4, 'text' => ''],
                ['id' => 5, 'text' => 'foo'],
            ]
        ], [
            'foo' => 'required',
            'email' => 'email',
            'something' => 'numeric',
            'comments.*.text' => 'required'
        ]);

        $validation->messages()->add('en', [
            'rule.required' => 'foo',
            'rule.email' => 'bar',
            'comments.*.text' => 'baz',
            'rule.numeric' => 'baz'
        ]);

        $validation->validate();

        $errors = $validation->errors();
        $this->assertEquals('foo', $errors->first('foo:required'));
        $this->assertEquals('bar', $errors->first('email:email'));
        $this->assertEquals('baz', $errors->first('something:numeric'));
        $this->assertEquals('baz', $errors->first('comments.0.text:required'));
    }

    public function testCustomMessageInCallbackRule()
    {
        $evenNumberValidator = function ($value) {
            if (!is_numeric($value) or $value % 2 !== 0) {
                return 'custom.rule.even_number';
            }
            return true;
        };

        $validation = $this->validator->make([
            'foo' => 'abc',
        ], [
            'foo' => [$evenNumberValidator],
        ]);
        $validation->messages()->replace('en', 'custom.rule.even_number', ':attribute must be even number');

        $validation->validate();

        $errors = $validation->errors();
        $this->assertEquals("foo must be even number", $errors->first('foo:callback'));
    }

    public function testCustomMessageInArrayValidation()
    {
        $validation = $this->validator->make([
            'cart' => [
                [
                    'qty' => 'xyz',
                    'itemName' => 'Lorem ipsum'
                ],
                [
                    'qty' => 10,
                    'attributes' => [
                        [
                            'name' => 'color',
                            'value' => null
                        ]
                    ]
                ],
            ]
        ], [
            'cart.*.itemName' => 'required',
            'cart.*.qty' => 'required|numeric',
            'cart.*.attributes.*.value' => 'required'
        ]);

        $validation->messages()->add('en', [
            'cart.*.itemName:required' => 'Item [0] name is required',
            'cart.*.qty:numeric' => 'Item {0} qty is not a number',
            'cart.*.attributes.*.value' => 'Item {0} attribute {1} value is required',
        ]);

        $validation->validate();

        $errors = $validation->errors();

        $this->assertEquals('Item 1 qty is not a number', $errors->first('cart.*.qty'));
        $this->assertEquals('Item 1 name is required', $errors->first('cart.*.itemName'));
        $this->assertEquals('Item 2 attribute 1 value is required', $errors->first('cart.*.attributes.*.value'));
    }

    public function testSpecificRuleMessage()
    {
        $validation = $this->validator->make([
            'something' => 'value',
        ], [
            'something' => 'email|max:3|numeric',
        ]);

        $validation->messages()->add('en', [
            'something:email' => 'foo',
            'something:numeric' => 'bar',
            'something:max' => 'baz',
        ]);

        $validation->validate();

        $errors = $validation->errors();
        $this->assertEquals('foo', $errors->first('something:email'));
        $this->assertEquals('bar', $errors->first('something:numeric'));
        $this->assertEquals('baz', $errors->first('something:max'));
    }

    public function testSetAttributeAliases()
    {
        $validation = $this->validator->make([
            'foo' => null,
            'email' => 'invalid email',
            'something' => 'not numeric',
            'comments' => [
                ['id' => 4, 'text' => ''],
                ['id' => 5, 'text' => 'foo'],
            ]
        ], [
            'foo' => 'required',
            'email' => 'email',
            'something' => 'numeric',
            'comments.*.text' => 'required'
        ]);

        $validation->messages()->add('en', [
            'rule.required' => ':attribute foo',
            'rule.email' => ':attribute bar',
            'rule.numeric' => ':attribute baz',
            'comments.*.text' => ':attribute qux'
        ]);

        $validation->setAlias('foo', 'Foo');
        $validation->setAlias('email', 'Bar');

        $validation->setAlias('something', 'Baz');
        $validation->setAlias('comments.*.text', 'Qux');

        $validation->validate();

        $errors = $validation->errors();
        $this->assertEquals('Foo foo', $errors->first('foo:required'));
        $this->assertEquals('Bar bar', $errors->first('email:email'));
        $this->assertEquals('Baz baz', $errors->first('something:numeric'));
        $this->assertEquals('Qux qux', $errors->first('comments.0.text:required'));
    }

    public function testGetValidData()
    {
        $validation = $this->validator->validate([
            'items' => [
                [
                    'product_id' => 1,
                    'qty' => 'invalid'
                ]
            ],
            'emails' => [
                'foo@bar.com',
                'something',
                'foo@blah.com'
            ],
            'stuffs' => [
                'one' => '1',
                'two' => '2',
                'three' => 'three',
            ],
            'thing' => 'exists',
        ], [
            'thing' => 'required',
            'items.*.product_id' => 'required|numeric',
            'emails.*' => 'required|email',
            'items.*.qty' => 'required|numeric',
            'something' => 'default:on|required|in:on,off',
            'stuffs' => 'required|array',
            'stuffs.one' => 'required|numeric',
            'stuffs.two' => 'required|numeric',
            'stuffs.three' => 'required|numeric',
        ]);

        $validData = $validation->getValidData();

        $this->assertEquals([
            'items' => [
                [
                    'product_id' => 1
                ]
            ],
            'emails' => [
                0 => 'foo@bar.com',
                2 => 'foo@blah.com'
            ],
            'thing' => 'exists',
            'something' => 'on',
            'stuffs' => [
                'one' => '1',
                'two' => '2',
            ]
        ], $validData);

        $stuffs = $validData['stuffs'];
        $this->assertFalse(isset($stuffs['three']));
    }

    public function testGetInvalidData()
    {
        $validation = $this->validator->validate([
            'items' => [
                [
                    'product_id' => 1,
                    'qty' => 'invalid'
                ]
            ],
            'emails' => [
                'foo@bar.com',
                'something',
                'foo@blah.com'
            ],
            'stuffs' => [
                'one' => '1',
                'two' => '2',
                'three' => 'three',
            ],
            'thing' => 'exists',
        ], [
            'thing' => 'required',
            'items.*.product_id' => 'required|numeric',
            'emails.*' => 'required|email',
            'items.*.qty' => 'required|numeric',
            'something' => 'required|in:on,off',
            'stuffs' => 'required|array',
            'stuffs.one' => 'numeric',
            'stuffs.two' => 'numeric',
            'stuffs.three' => 'numeric',
        ]);

        $invalidData = $validation->getInvalidData();

        $this->assertEquals([
            'items' => [
                [
                    'qty' => 'invalid'
                ]
            ],
            'emails' => [
                1 => 'something'
            ],
            'something' => null,
            'stuffs' => [
                'three' => 'three',
            ]
        ], $invalidData);

        $stuffs = $invalidData['stuffs'];
        $this->assertFalse(isset($stuffs['one']));
        $this->assertFalse(isset($stuffs['two']));
    }

    public function testArrayOfRules()
    {
        $validation = $this->validator->validate([
            'number' => '1.2345'
        ], [
            'number' => ['numeric', 'max' => 2],
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testLoadMessagesFromFile()
    {
        Closure::bind(fn () => $this->messages = new MessageBag(), $this->validator, Factory::class)();

        $this->validator->registerLanguageMessages('en', __DIR__ . '/Fixtures/pirate.php');

        $validation = $this->validator->validate([
            'number' => 'foobar'
        ], [
            'number' => ['numeric'],
        ]);

        $this->assertEquals('yar, number neigh thar', $validation->errors()->first('number'));
    }
}
