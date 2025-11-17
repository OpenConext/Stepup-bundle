<?php

/**
 * Copyright 2025 SURFnet bv
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
