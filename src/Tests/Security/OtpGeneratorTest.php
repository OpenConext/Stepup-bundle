<?php

/**
 * Copyright 2014 SURFnet bv
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

namespace Surfnet\StepupBundle\Security;

use PHPUnit\Framework\TestCase ;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;

final class OtpGeneratorTest extends TestCase
{
    /**
     * @test
     * @group security
     */
    public function it_generates_eight_character_otp_strings()
    {
        $otp = OtpGenerator::generate(8);

        $this->assertIsString($otp);
        $this->assertSame(8, strlen($otp), 'OTP is not eight characters long');
    }

    public function nonPositiveIntegers()
    {
        return [
            'null' => [null],
            'false' => [false],
            'true' => [true],
            'string' => ['8'],
            'object' => [new \stdClass()],
            'array' => [['foo', 'bar']],
            'negative integer' => [-1],
            'zero' => [0],
        ];
    }

    /**
     * @test
     * @group security
     * @dataProvider nonPositiveIntegers
     *
     * @param mixed $length
     */
    public function it_cannot_generate_otp_strings_of_negative_or_non_integer_length($length)
    {
        $this->expectException(InvalidArgumentException::class);

        OtpGenerator::generate($length);
    }
}
