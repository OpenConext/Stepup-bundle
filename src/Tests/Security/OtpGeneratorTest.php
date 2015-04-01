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

use PHPUnit_Framework_TestCase as TestCase;

final class OtpGeneratorTest extends TestCase
{
    /**
     * @test
     * @group security
     */
    public function it_generates_eight_character_otp_strings()
    {
        $otp = OtpGenerator::generate();

        $this->assertInternalType('string', $otp);
        $this->assertSame(8, strlen($otp), 'OTP is not eight characters long');
    }

    /**
     * @test
     * @group security
     */
    public function it_only_uses_the_fixed_alphabet()
    {
        for ($i = 0; $i < 100; $i++) {
            $this->assertSame('', str_replace(str_split(OtpGenerator::ALPHABET), '', OtpGenerator::generate()));
        }
    }
}
