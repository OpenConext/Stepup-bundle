<?php

declare(strict_types = 1);

namespace Surfnet\Stepup\Tests\Helper;

use PHPUnit\Framework\TestCase;
use StdClass;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Exception\JsonException;
use Surfnet\StepupBundle\Http\JsonHelper;

class JsonHelperTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('json')]
    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('nonStringProvider')]
    public function jsonHelperCanOnlyDecodeStrings(null|bool|array|int|float|StdClass $nonString): void
    {
        $this->expectException(InvalidArgumentException::class);
        JsonHelper::decode($nonString);
    }

    #[\PHPUnit\Framework\Attributes\Group('json')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function jsonHelperDecodesStringsToArrays(): void
    {
        $expectedDecodedResult = ['hello' => 'world'];
        $json                  = '{ "hello" : "world" }';
        $actualDecodedResult = JsonHelper::decode($json);
        $this->assertSame($expectedDecodedResult, $actualDecodedResult);
    }

    #[\PHPUnit\Framework\Attributes\Group('json')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function jsonHelperThrowsAnExceptionWhenThereIsASyntaxError(): void
    {
        $this->expectException(JsonException::class);
        $jsonWithMissingDoubleQuotes = '{ hello : world }';
        JsonHelper::decode($jsonWithMissingDoubleQuotes);
    }

    public static function nonStringProvider(): array
    {
        return [
            'null'    => [null],
            'boolean' => [true],
            'array'   => [[]],
            'integer' => [1],
            'float'   => [1.2],
            'object'  => [new StdClass()],
        ];
    }
}
