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

namespace Surfnet\StepupBundle\Tests\Value;

use PHPUnit\Framework\TestCase ;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Value\YubikeyOtp;
use Surfnet\StepupBundle\Value\YubikeyPublicId;

final class YubikeyPublicIdTest extends TestCase
{
    public function invalidTypeProvider()
    {
        return [
            'unknown level' => [4],
            'object'        => [new \stdClass()],
            'float'         => [1.1],
            'boolean'       => [false],
            'resource'      => [fopen('php://memory', 'r')],
            'null'          => [null],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider invalidTypeProvider
     *
     * @param mixed $nonString
     */
    public function it_cannot_be_constructed_with_anything_but_a_string($nonString)
    {
        $this->expectExceptionMessage("Invalid Argument, parameter \"value\" should be of type \"string\"");
        $this->expectException(InvalidArgumentException::class);
        new YubikeyPublicId($nonString);
    }

    public function invalidFormatProvider()
    {
        return [
            '7-character unpadded ID'           => ['1906381'],
            '9-character padded ID'             => ['0123456789'],
            '19-character padded ID'            => ['01234567890123456789'],
            '21-character ID'                   => ['101234567890123456789'],
            'empty ID'                          => [''],
            'ID with alphabetical characters'   => ['abc'],
            'ID with alphanumerical characters' => ['abc01908389'],
            'Larger than 0xffffffffffffffff'    => ['18446744073709551616']
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider invalidFormatProvider
     *
     * @param mixed $invalidFormat
     */
    public function it_cannot_be_constructed_with_an_invalid_format($invalidFormat)
    {
        $this->expectException(InvalidArgumentException::class);
        new YubikeyPublicId($invalidFormat);
    }

    public function validFormatProvider()
    {
        return [
            '8-character ID'  => ['01906381'],
            '1-character ID'  => ['00000001'],
            '0-character ID'  => ['00000000'],
            '16-character ID' => ['1234560123456789'],
            '20-character ID' => ['12345678901234567890'],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider validFormatProvider
     *
     * @param string $validFormat
     */
    public function its_value_matches_its_input_value($validFormat)
    {
        $id = new YubikeyPublicId($validFormat);

        $this->assertEquals($validFormat, $id->getYubikeyPublicId());
    }

    public function otpProvider()
    {
        return [
            'Maximum value' => ['vvvvvvvvvvvvvvvvbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', '18446744073709551615'],
            'Minimum value' => ['cbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', '00000000'],
            'Real-life ID'  => ['ccccccbtbhnhbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', '01906358'],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider otpProvider
     *
     * @param string $otpString
     * @param string $yubikeyPublicId
     */
    public function it_accepts_valid_modhex_formats($otpString, $yubikeyPublicId)
    {
        $otp = YubikeyOtp::fromString($otpString);
        $id  = YubikeyPublicId::fromOtp($otp);

        $this->assertEquals($yubikeyPublicId, $id->getYubikeyPublicId());
    }

    /**
     * @test
     * @group value
     */
    public function it_can_check_for_equality()
    {
        $id = new YubikeyPublicId('01908382');
        $same = new YubikeyPublicId('01908382');
        $different = new YubikeyPublicId('987654322');

        $this->assertTrue($id->equals($same), 'Equal YubikeyPublicId are not equal');
        $this->assertFalse($id->equals($different), 'Dissimilar YubikeyPublicId are equal');
    }
}
