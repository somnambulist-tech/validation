<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Rule;
use Somnambulist\Components\Validation\Validation;
use function count;
use function is_array;

class ValidationTest extends TestCase
{
    /**
     * @param string $rules
     * @param array  $expectedResult
     *
     * @dataProvider parseRuleProvider
     */
    public function testParseRule($rules, $expectedResult)
    {
        $class  = new ReflectionClass(Validation::class);
        $method = $class->getMethod('parseRule');
        $method->setAccessible(true);

        $validation = new Validation(new Factory(), [], []);

        $result = $method->invokeArgs($validation, [$rules]);
        $this->assertSame($expectedResult, $result);
    }

    public function parseRuleProvider(): array
    {
        return [
            [
                'email',
                [
                    'email',
                    [],
                ],
            ],
            [
                'min:6',
                [
                    'min',
                    ['6'],
                ],
            ],
            [
                'uploaded_file:0,500K,png,jpeg',
                [
                    'uploaded_file',
                    ['0', '500K', 'png', 'jpeg'],
                ],
            ],
            [
                'same:password',
                [
                    'same',
                    ['password'],
                ],
            ],
            [
                'regex:/^([a-zA-Z\,]*)$/',
                [
                    'regex',
                    ['/^([a-zA-Z\,]*)$/'],
                ],
            ],
            [
                'date:Y-m-d H:i:s',
                [
                    'date',
                    ['Y-m-d H:i:s'],
                ],
            ],
        ];
    }

    public function testValidateHandlesColonsInRuleAttributes()
    {
        $factory = new Factory();
        $factory->addRule('order', new class extends Rule {
            public function check(mixed $value): bool
            {
                return true;
            }

            public function fillParameters(array $params): self
            {
                Assert::assertEquals('id:ASC', $params[0]);
                Assert::assertEquals('createdAt:DESC', $params[1]);

                return $this;
            }
        });

        $validation = $factory->validate([
            'orderBy' => 'id:ASC',
            'date'    => '2022-09-11 19:55:00',
        ], [
            'orderBy' => 'order:id:ASC,createdAt:DESC',
            'date'    => [
                'date:Y-m-d H:i:s',
                'date' => 'Y-m-d H:i:s',
            ]
        ]);

        $this->assertTrue($validation->passes());

        $validation = $factory->validate([
            'orderBy' => 'id:ASC',
            'date'    => '2022-09-11',
        ], [
            'orderBy' => 'order:id:ASC,createdAt:DESC',
            'date'    => [
                'date:Y-m-d H:i:s',
                'date' => 'Y-m-d H:i:s',
            ]
        ]);

        $this->assertFalse($validation->passes());
        $this->assertArrayHasKey('date', $validation->getInvalidData());
    }
}
