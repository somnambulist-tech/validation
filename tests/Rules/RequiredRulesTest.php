<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

class RequiredRulesTest extends TestCase
{
    protected ?Factory $validator = null;

    protected function setUp(): void
    {
        $this->validator = new Factory;
    }

    public function testRequiredIfRule()
    {
        $v1 = $this->validator->validate([
            'a' => '',
            'b' => '',
        ], [
            'b' => 'required_if:a,1'
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
        ], [
            'b' => 'required_if:a,1'
        ]);

        $this->assertFalse($v2->passes());
    }

    public function testRequiredUnlessRule()
    {
        $v1 = $this->validator->validate([
            'a' => '',
            'b' => '',
        ], [
            'b' => 'required_unless:a,1'
        ]);

        $this->assertFalse($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
        ], [
            'b' => 'required_unless:a,1'
        ]);

        $this->assertTrue($v2->passes());
    }

    public function testRequiresRule()
    {
        $v1 = $this->validator->validate([
            'b' => '',
        ], [
            'b' => 'requires:a'
        ]);

        $this->assertFalse($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '',
            'b' => '',
        ], [
            'b' => 'requires:a'
        ]);

        $this->assertFalse($v2->passes());

        $v3 = $this->validator->validate([
            'a' => null,
            'b' => '',
        ], [
            'b' => 'requires:a'
        ]);

        $this->assertFalse($v3->passes());

        $v4 = $this->validator->validate([
            'a' => '23',
            'b' => '',
        ], [
            'b' => 'requires:a'
        ]);
        $this->assertTrue($v4->passes());
    }

    public function testRequiredWithRule()
    {
        $v1 = $this->validator->validate([
            'b' => '',
        ], [
            'b' => 'required_with:a'
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
        ], [
            'b' => 'required_with:a'
        ]);

        $this->assertFalse($v2->passes());
    }

    public function testRequiredWithoutRule()
    {
        $v1 = $this->validator->validate([
            'b' => '',
        ], [
            'b' => 'required_without:a'
        ]);

        $this->assertFalse($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
        ], [
            'b' => 'required_without:a'
        ]);

        $this->assertTrue($v2->passes());
    }

    public function testRequiredWithAllRule()
    {
        $v1 = $this->validator->validate([
            'b' => '',
            'a' => '1'
        ], [
            'b' => 'required_with_all:a,c'
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '1',
            'b' => '',
            'c' => '2'
        ], [
            'b' => 'required_with_all:a,c'
        ]);

        $this->assertFalse($v2->passes());
    }

    public function testRequiredWithoutAllRule()
    {
        $v1 = $this->validator->validate([
            'b' => 'foo',
            'a' => null,
            'c' => null,
        ], [
            'b' => 'required_without_all:a,c'
        ]);

        $this->assertTrue($v1->passes());

        $v1 = $this->validator->validate([
            'b' => '',
            'a' => '1'
        ], [
            'b' => 'required_without_all:a,c'
        ]);

        $this->assertFalse($v1->passes());

        $v2 = $this->validator->validate([
            'b' => '',
        ], [
            'b' => 'required_without_all:a,c'
        ]);

        $this->assertFalse($v2->passes());
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

    public function testRequiredIfOnArrayAttribute()
    {
        $validation = $this->validator->validate([
            'products' => [
                // invalid because has_notes is not empty
                '10' => [
                    'quantity' => 8,
                    'has_notes' => 1,
                    'notes' => ''
                ],
                // valid because has_notes is null
                '12' => [
                    'quantity' => 0,
                    'has_notes' => null,
                    'notes' => ''
                ],
                // valid because no has_notes
                '14' => [
                    'quantity' => 0,
                    'notes' => ''
                ],
            ]
        ], [
            'products.*.notes' => 'required_if:products.*.has_notes,1',
        ]);

        $this->assertFalse($validation->passes());

        $errors = $validation->errors();
        $this->assertNotNull($errors->first('products.10.notes'));
        $this->assertNull($errors->first('products.12.notes'));
        $this->assertNull($errors->first('products.14.notes'));
    }

    public function testRequiredUnlessOnArrayAttribute()
    {
        $validation = $this->validator->validate([
            'products' => [
                // valid because has_notes is 1
                '10' => [
                    'quantity' => 8,
                    'has_notes' => 1,
                    'notes' => ''
                ],
                // invalid because has_notes is not 1
                '12' => [
                    'quantity' => 0,
                    'has_notes' => null,
                    'notes' => ''
                ],
                // invalid because no has_notes
                '14' => [
                    'quantity' => 0,
                    'notes' => ''
                ],
            ]
        ], [
            'products.*.notes' => 'required_unless:products.*.has_notes,1',
        ]);

        $this->assertFalse($validation->passes());

        $errors = $validation->errors();
        $this->assertNull($errors->first('products.10.notes'));
        $this->assertNotNull($errors->first('products.12.notes'));
        $this->assertNotNull($errors->first('products.14.notes'));
    }
}
