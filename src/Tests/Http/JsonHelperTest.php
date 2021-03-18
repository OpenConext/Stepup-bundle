<?php

namespace Surfnet\Stepup\Tests\Helper;

use PHPUnit\Framework\TestCase ;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Exception\JsonException;
use Surfnet\StepupBundle\Http\JsonHelper;

class JsonHelperTest extends TestCase
{
    /**
     * @test
     * @group json
     *
     * @dataProvider nonStringProvider
     * @param $nonString
     */
    public function jsonHelperCanOnlyDecodeStrings($nonString)
    {
        $this->expectException(InvalidArgumentException::class);
        JsonHelper::decode($nonString);
    }

    /**
     * @test
     * @group json
     */
    public function jsonHelperDecodesStringsToArrays()
    {
        $expectedDecodedResult = ['hello' => 'world'];
        $json                  = '{ "hello" : "world" }';
        $actualDecodedResult = JsonHelper::decode($json);
        $this->assertSame($expectedDecodedResult, $actualDecodedResult);
    }

    /**
     * @test
     * @group json
     */
    public function jsonHelperThrowsAnExceptionWhenThereIsASyntaxError()
    {
        $this->expectException(JsonException::class, 'Syntax error');
        $jsonWithMissingDoubleQuotes = '{ hello : world }';
        JsonHelper::decode($jsonWithMissingDoubleQuotes);
    }

    public function nonStringProvider()
    {
        return [
            'null'    => [null],
            'boolean' => [true],
            'array'   => [[]],
            'integer' => [1],
            'float'   => [1.2],
            'object'  => [new \StdClass()],
        ];
    }
}
