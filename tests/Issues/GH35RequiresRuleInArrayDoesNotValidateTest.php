<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Issues;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;

class GH35RequiresRuleInArrayDoesNotValidateTest extends TestCase
{
    /**
     * @link https://github.com/somnambulist-tech/validation/issues/35
     */
    public function testRequiresRuleInArrayValidatesFields()
    {
        $testData = [
            'events' => [
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => null,
                ],
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => '2025-04-12 10:50:00',
                ],
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => null,
                ],
            ],
        ];

        $validation = (new Factory)->validate($testData, [
            'events'         => 'array',
            'events.*.start' => 'requires:events.*.end|date:Y-m-d H:i:s',
            'events.*.end'   => 'requires:events.*.start|date:Y-m-d H:i:s',
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals('events.0.start requires "events.0.end"', $validation->errors()->first('events.0.start'));
        $this->assertEquals('events.2.start requires "events.2.end"', $validation->errors()->first('events.2.start'));
    }

    public function testRequiredWithValidatesArray()
    {
        $testData = [
            'events' => [
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => null,
                ],
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => '2025-04-12 10:50:00',
                ],
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => null,
                ],
            ],
        ];

        $validation = (new Factory)->validate($testData, [
            'events'         => 'array',
            'events.*.start' => 'required_with:events.*.end|date:Y-m-d H:i:s',
            'events.*.end'   => 'required_with:events.*.start|date:Y-m-d H:i:s',
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals('events.0.end is required with "events.0.start"', $validation->errors()->first('events.0.end'));
        $this->assertEquals('events.2.end is required with "events.2.start"', $validation->errors()->first('events.2.end'));
    }

    public function testRequiredWithAllValidatesArray()
    {
        $testData = [
            'events' => [
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => null,
                ],
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => '2025-04-12 10:50:00',
                ],
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => null,
                ],
            ],
        ];

        $validation = (new Factory)->validate($testData, [
            'events'         => 'array',
            'events.*.start' => 'required_with_all:events.*.end|date:Y-m-d H:i:s',
            'events.*.end'   => 'required_with_all:events.*.start|date:Y-m-d H:i:s',
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals('events.0.end is required with all of "events.0.start"', $validation->errors()->first('events.0.end'));
        $this->assertEquals('events.2.end is required with all of "events.2.start"', $validation->errors()->first('events.2.end'));
    }

    public function testRequiredWithoutValidatesArray()
    {
        $testData = [
            'events' => [
                [
                    'end'   => null,
                    'start' => null,
                ],
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => '2025-04-12 10:50:00',
                ],
            ],
        ];

        $validation = (new Factory)->validate($testData, [
            'events'         => 'array',
            'events.*.start' => 'required_without:events.*.end|date:Y-m-d H:i:s',
            'events.*.end'   => 'required_without:events.*.start|date:Y-m-d H:i:s',
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals('events.0.end is required when "events.0.start" are empty', $validation->errors()->first('events.0.end'));
    }

    public function testRequiredWithoutAllValidatesArray()
    {
        $testData = [
            'events' => [
                [
                    'end'   => null,
                    'start' => null,
                ],
                [
                    'start' => '2025-04-12 10:49:00',
                    'end'   => '2025-04-12 10:50:00',
                ],
                [
                    'end'   => null,
                    'start' => null,
                ],
            ],
        ];

        $validation = (new Factory)->validate($testData, [
            'events'         => 'array',
            'events.*.start' => 'required_without_all:events.*.end|date:Y-m-d H:i:s',
            'events.*.end'   => 'required_without_all:events.*.start|date:Y-m-d H:i:s',
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals('events.0.end is required when "events.0.start" are all empty', $validation->errors()->first('events.0.end'));
        $this->assertEquals('events.2.end is required when "events.2.start" are all empty', $validation->errors()->first('events.2.end'));
    }
}
