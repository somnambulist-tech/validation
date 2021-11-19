<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Factory;
use Somnambulist\Components\Validation\Rules\UploadedFile;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

/**
 * Class UploadedFileTest
 *
 * @package    Somnambulist\Components\Validation\Tests
 * @subpackage Somnambulist\Components\Validation\Tests\UploadedFileTest
 */
class UploadedFileTest extends TestCase
{
    protected ?Factory $validator = null;

    protected function setUp(): void
    {
        $this->validator = new Factory;
    }

    public function testRequiredUploadedFile()
    {
        $empty_file = [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];

        $validation = $this->validator->validate([
            'file' => $empty_file
        ], [
            'file' => 'required|uploaded_file'
        ]);

        $errors = $validation->errors();
        $this->assertFalse($validation->passes());
        $this->assertNotNull($errors->first('file:required'));
    }

    public function testOptionalUploadedFile()
    {
        $emptyFile = [
            'name' => '',
            'type' => '',
            'size' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];

        $validation = $this->validator->validate([
            'file' => $emptyFile
        ], [
            'file' => 'uploaded_file'
        ]);
        $this->assertTrue($validation->passes());
    }

    /**
     * @dataProvider getSamplesMissingKeyFromUploadedFileValue
     */
    public function testMissingKeyUploadedFile($uploadedFile)
    {
        $validation = $this->validator->validate([
            'file' => $uploadedFile
        ], [
            'file' => 'required|uploaded_file'
        ]);

        $errors = $validation->errors();
        $this->assertFalse($validation->passes());
        $this->assertNotNull($errors->first('file:required'));
    }

    public function getSamplesMissingKeyFromUploadedFileValue()
    {
        $validUploadedFile = [
            'name' => 'foo',
            'type' => 'text/plain',
            'size' => 1000,
            'tmp_name' => __FILE__,
            'error' => UPLOAD_ERR_OK
        ];

        $samples = [];

        foreach ($validUploadedFile as $key => $value) {
            $uploadedFile = $validUploadedFile;
            unset($uploadedFile[$key]);
            $samples[] = $uploadedFile;
        }

        return $samples;
    }

    public function testValidationShouldCorrectlyResolveMultipleFileUploads()
    {
        // Test from input files:
        // <input type="file" name="photos[]"/>
        // <input type="file" name="photos[]"/>
        $sampleInputFiles = [
            'photos' => [
                'name' => [
                    'a.png',
                    'b.jpeg',
                ],
                'type' => [
                    'image/png',
                    'image/jpeg',
                ],
                'size' => [
                    1000,
                    2000,
                ],
                'tmp_name' => [
                    __DIR__.'/a.png',
                    __DIR__.'/b.jpeg',
                ],
                'error' => [
                    UPLOAD_ERR_OK,
                    UPLOAD_ERR_OK,
                ]
            ]
        ];

        $uploadedFileRule = $this->getMockedUploadedFileRule()->types('jpeg');

        $validation = $this->validator->validate($sampleInputFiles, [
            'photos.*' => ['required', $uploadedFileRule]
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals($validation->getValidData(), [
            'photos' => [
                1 => [
                    'name' => 'b.jpeg',
                    'type' => 'image/jpeg',
                    'size' => 2000,
                    'tmp_name' => __DIR__.'/b.jpeg',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ]);
        $this->assertEquals($validation->getInvalidData(), [
            'photos' => [
                0 => [
                    'name' => 'a.png',
                    'type' => 'image/png',
                    'size' => 1000,
                    'tmp_name' => __DIR__.'/a.png',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ]);
    }

    public function testValidationShouldCorrectlyResolveAssocFileUploads()
    {
        // Test from input files:
        // <input type="file" name="photos[foo]"/>
        // <input type="file" name="photos[bar]"/>
        $sampleInputFiles = [
            'photos' => [
                'name' => [
                    'foo' => 'a.png',
                    'bar' => 'b.jpeg',
                ],
                'type' => [
                    'foo' => 'image/png',
                    'bar' => 'image/jpeg',
                ],
                'size' => [
                    'foo' => 1000,
                    'bar' => 2000,
                ],
                'tmp_name' => [
                    'foo' => __DIR__.'/a.png',
                    'bar' => __DIR__.'/b.jpeg',
                ],
                'error' => [
                    'foo' => UPLOAD_ERR_OK,
                    'bar' => UPLOAD_ERR_OK,
                ]
            ]
        ];

        $uploadedFileRule = $this->getMockedUploadedFileRule()->types('jpeg');

        $validation = $this->validator->validate($sampleInputFiles, [
            'photos.foo' => ['required', clone $uploadedFileRule],
            'photos.bar' => ['required', clone $uploadedFileRule],
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals($validation->getValidData(), [
            'photos' => [
                'bar' => [
                    'name' => 'b.jpeg',
                    'type' => 'image/jpeg',
                    'size' => 2000,
                    'tmp_name' => __DIR__.'/b.jpeg',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ]);
        $this->assertEquals($validation->getInvalidData(), [
            'photos' => [
                'foo' => [
                    'name' => 'a.png',
                    'type' => 'image/png',
                    'size' => 1000,
                    'tmp_name' => __DIR__.'/a.png',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ]);
    }

    public function testValidationShouldCorrectlyResolveComplexFileUploads()
    {
        // Test from input files:
        // <input type="file" name="files[foo][bar][baz]"/>
        // <input type="file" name="files[foo][bar][qux]"/>
        // <input type="file" name="files[photos][]"/>
        // <input type="file" name="files[photos][]"/>
        $sampleInputFiles = [
            'files' => [
                'name' => [
                    'foo' => [
                        'bar' => [
                            'baz' => 'foo-bar-baz.jpeg',
                            'qux' => 'foo-bar-qux.png',
                        ]
                    ],
                    'photos' => [
                        'photos-0.png',
                        'photos-1.jpeg',
                    ]
                ],
                'type' => [
                    'foo' => [
                        'bar' => [
                            'baz' => 'image/jpeg',
                            'qux' => 'image/png',
                        ]
                    ],
                    'photos' => [
                        'image/png',
                        'image/jpeg',
                    ]
                ],
                'size' => [
                    'foo' => [
                        'bar' => [
                            'baz' => 500,
                            'qux' => 750,
                        ]
                    ],
                    'photos' => [
                        1000,
                        2000,
                    ]
                ],
                'tmp_name' => [
                    'foo' => [
                        'bar' => [
                            'baz' => __DIR__.'/foo-bar-baz.jpeg',
                            'qux' => __DIR__.'/foo-bar-qux.png',
                        ]
                    ],
                    'photos' => [
                        __DIR__.'/photos-0.png',
                        __DIR__.'/photos-1.jpeg',
                    ]
                ],
                'error' => [
                    'foo' => [
                        'bar' => [
                            'baz' => UPLOAD_ERR_OK,
                            'qux' => UPLOAD_ERR_OK,
                        ]
                    ],
                    'photos' => [
                        UPLOAD_ERR_OK,
                        UPLOAD_ERR_OK,
                    ]
                ]
            ]
        ];

        $uploadedFileRule = $this->getMockedUploadedFileRule()->types('jpeg');

        $validation = $this->validator->validate($sampleInputFiles, [
            'files.foo.bar.baz' => ['required', clone $uploadedFileRule],
            'files.foo.bar.qux' => ['required', clone $uploadedFileRule],
            'files.photos.*' => ['required', clone $uploadedFileRule],
        ]);

        $this->assertFalse($validation->passes());
        $this->assertEquals($validation->getValidData(), [
            'files' => [
                'foo' => [
                    'bar' => [
                        'baz' => [
                            'name' => 'foo-bar-baz.jpeg',
                            'type' => 'image/jpeg',
                            'size' => 500,
                            'tmp_name' => __DIR__.'/foo-bar-baz.jpeg',
                            'error' => UPLOAD_ERR_OK,
                        ]
                    ]
                ],
                'photos' => [
                    1 => [
                        'name' => 'photos-1.jpeg',
                        'type' => 'image/jpeg',
                        'size' => 2000,
                        'tmp_name' => __DIR__.'/photos-1.jpeg',
                        'error' => UPLOAD_ERR_OK,
                    ]
                ]
            ]
        ]);
        $this->assertEquals($validation->getInvalidData(), [
            'files' => [
                'foo' => [
                    'bar' => [
                        'qux' => [
                            'name' => 'foo-bar-qux.png',
                            'type' => 'image/png',
                            'size' => 750,
                            'tmp_name' => __DIR__.'/foo-bar-qux.png',
                            'error' => UPLOAD_ERR_OK,
                        ]
                    ]
                ],
                'photos' => [
                    0 => [
                        'name' => 'photos-0.png',
                        'type' => 'image/png',
                        'size' => 1000,
                        'tmp_name' => __DIR__.'/photos-0.png',
                        'error' => UPLOAD_ERR_OK,
                    ],
                ]
            ]
        ]);
    }

    public function getMockedUploadedFileRule()
    {
        $rule = $this->getMockBuilder(UploadedFile::class)
            ->onlyMethods(['isUploadedFile'])
            ->getMock();

        $rule->method('isUploadedFile')->willReturn(true);

        return $rule;
    }

    public function testRuleMimesInvalidMessages()
    {
        $file = [
            'name' => 'sample.txt',
            'type' => 'plain/text',
            'tmp_name' => __FILE__,
            'size' => 1000,
            'error' => UPLOAD_ERR_OK,
        ];

        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => 'mimes:jpeg,png,bmp',
        ]);

        $expectedMessage = 'sample file type must be "jpeg", "png", "bmp"';
        $this->assertEquals($expectedMessage, $validation->errors()->first('sample'));
    }

    public function testRuleUploadedFileInvalidMessages()
    {
        $file = [
            'name' => 'sample.txt',
            'type' => 'plain/text',
            'tmp_name' => __FILE__,
            'size' => 1024 * 1024 * 2, // 2M
            'error' => UPLOAD_ERR_OK,
        ];

        $rule = $this->getMockedUploadedFileRule();

        // Invalid uploaded file (!is_uploaded_file($file['tmp_name']))
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => 'uploaded_file',
        ]);

        $expectedMessage = "sample is not a valid uploaded file";
        $this->assertEquals($expectedMessage, $validation->errors()->first('sample'));

        // Invalid min size
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => [(clone $rule)->minSize('3M')],
        ]);

        $expectedMessage = "sample file is too small, minimum size is 3M";
        $this->assertEquals($expectedMessage, $validation->errors()->first('sample'));

        // Invalid max size
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => [(clone $rule)->maxSize('1M')],
        ]);

        $expectedMessage = "sample file is too large, maximum size is 1M";
        $this->assertEquals($expectedMessage, $validation->errors()->first('sample'));

        // Invalid file types
        $validation = $this->validator->validate([
            'sample' => $file,
        ], [
            'sample' => [(clone $rule)->types(['jpeg', 'png', 'bmp'])],
        ]);

        $expectedMessage = 'sample file type must be "jpeg", "png", "bmp"';
        $this->assertEquals($expectedMessage, $validation->errors()->first('sample'));
    }

    public function testUploadedFileFunctionsWithRequiredIf()
    {
        $v1 = $this->validator->validate([
            'b' => '',
        ], [
            'a' => 'required_if:b,foo|uploaded_file:0,10M,pdf,jpeg,jpg'
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '',
            'b' => 'foo',
        ], [
            'a' => 'required_if:b,foo|uploaded_file:0,10M,pdf,jpeg,jpg'
        ]);

        $this->assertFalse($v2->passes());
        $this->assertEquals('a is required if b has a value of "foo"', $v2->errors()->first('a'));
    }

    public function testUploadedFileFunctionsWithRequiredWhen()
    {
        $v1 = $this->validator->validate([
            'a' => '',
        ], [
            'b' => 'nullable',
            'a' => 'required_with:b|uploaded_file:0,10M,pdf,jpeg,jpg',
        ]);

        $this->assertTrue($v1->passes());

        $v2 = $this->validator->validate([
            'a' => '',
            'b' => 'foo',
        ], [
            'a' => 'required_with:b|uploaded_file:0,10M,pdf,jpeg,jpg'
        ]);

        $this->assertFalse($v2->passes());
        $this->assertEquals('a is required with "b"', $v2->errors()->first('a'));
    }
}
