<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Issues;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

class GH26HandlingCallbackErrorMessagesTest extends TestCase
{
    /**
     * @link https://github.com/somnambulist-tech/validation/issues/26
     */
    public function testReturningMessageStringsProducesErrorMessage()
    {
        $testData = [
            'name'   => 'John Doe',
            'custom' => 'foo',
        ];

        $validation = (new Factory)->validate($testData, [
            'name'   => 'required',
            'custom' => [
                'required',
                function ($value) {
                    if ($value !== 'bar') {
                        return ':attribute should be bar';
                    }

                    return true;
                },
            ],
        ]);

        $this->assertEquals('custom should be bar', $validation->errors()->first('custom'));
    }

    public function testCanSetMessageIdentityViaCallback()
    {
        $testData = [
            'name'   => 'John Doe',
            'custom' => 'foo',
        ];

        $validation = (new Factory)->validate($testData, [
            'name'   => 'required',
            'custom' => [
                'required',
                function ($value) {
                    $this->message = ':attribute should be bar';

                    return $value === 'bar';
                },
            ],
        ]);

        $this->assertEquals('custom should be bar', $validation->errors()->first('custom'));
    }
}
