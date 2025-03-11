<?php declare(strict_types=1);


namespace Somnambulist\Components\Validation\Tests\Rules;

use DateTime;
use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rules\After;

class AfterTest extends TestCase
{

    /**
     * @var After
     */
    protected $validator;

    public function setUp(): void
    {
        $this->validator = new After();
    }

    /**
     * @dataProvider getValidDates
     */
    public function testOnlyAWellFormedDateCanBeValidated($date)
    {
        $this->assertTrue(
            $this->validator->fillParameters(["3 years ago"])->check($date)
        );
    }

    /**
     * @dataProvider getInvalidDates
     */
    public function testANonWellFormedDateCannotBeValidated($date)
    {
        $this->expectException(ParameterException::class);

        $this->validator->fillParameters(["tomorrow"])->check($date);
    }

    public function testUserProvidedParamCannotBeValidatedBecauseItIsInvalid()
    {
        $this->expectException(ParameterException::class);

        $this->validator->fillParameters(["to,morrow"])->check("now");
    }

    public static function getInvalidDates(): array
    {
        $now = new DateTime();

        return [
            ["12"], //12 instead of 2012
            ["09"], //like '09 instead of 2009
            [$now->format("Y m d")],
            [$now->format("Y m d h:i:s")],
            ["tommorow"], //typo
            ["lasst year"], //typo
            ["0123"] //invalid unix
        ];
    }

    public static function getValidDates(): array
    {
        $now = new DateTime();

        return [
            ['2016'],
            [$now->format("Y-m-d")],
            [$now->format("Y-m-d h:i:s")],
            ["now"],
            ["tomorrow"],
            ["2 years ago"],
            ["1741716133"]
        ];
    }

    public function testProvidedDateFailsValidation()
    {

        $now = (new DateTime("today"))->format("Y-m-d");
        $today = "today";

        $this->assertFalse(
            $this->validator->fillParameters(['tomorrow'])->check($now)
        );

        $this->assertFalse(
            $this->validator->fillParameters(['tomorrow'])->check($today)
        );
    }
}
