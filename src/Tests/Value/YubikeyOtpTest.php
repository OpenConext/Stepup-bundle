<?php

declare(strict_types = 1);

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

use PHPUnit\Framework\TestCase;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Value\YubikeyOtp;

class YubikeyOtpTest extends TestCase
{
    public function otpStrings(): array
    {
        return [
            'Regular OTP' => [
                'ddddddbtbhnhcjnkcfeiegrrnnednjcluulduerelthv',
                'ddddddbtbhnhcjnkcfeiegrrnnednjcluulduerelthv',
                '',
                'ddddddbtbhnh',
                'cjnkcfeiegrrnnednjcluulduerelthv'
            ],
            'Password OTP' => [
                'passwd:ddddddbtbhnhcjnkcfeiegrrnnednjcluulduerelthv',
                'ddddddbtbhnhcjnkcfeiegrrnnednjcluulduerelthv',
                'passwd',
                'ddddddbtbhnh',
                'cjnkcfeiegrrnnednjcluulduerelthv'
            ],
            'Short public id' => [
                'vvvvvcjnkcfeiegrrnnednjcluulduerelthv',
                'vvvvvcjnkcfeiegrrnnednjcluulduerelthv',
                '',
                'vvvvv',
                'cjnkcfeiegrrnnednjcluulduerelthv'
            ],
            'Long public id' => [
                'ccccddddeeeeffffcjnkcfeiegrrnnednjcluulduerelthv',
                'ccccddddeeeeffffcjnkcfeiegrrnnednjcluulduerelthv',
                '',
                'ccccddddeeeeffff',
                'cjnkcfeiegrrnnednjcluulduerelthv'
            ],
            'Dvorak OTP' => [
                'jxe.uidchtnbpygkjxe.uidchtnbpygkjxe.uidchtnbpygk',
                'cbdefghijklnrtuvcbdefghijklnrtuvcbdefghijklnrtuv',
                '',
                'cbdefghijklnrtuv',
                'cbdefghijklnrtuvcbdefghijklnrtuv'
            ],
            'Dvorak OTP w/ password' => [
                'passwd:jxe.uidchtnbpygkjxe.uidchtnbpygkjxe.uidchtnbpygk',
                'cbdefghijklnrtuvcbdefghijklnrtuvcbdefghijklnrtuv',
                'passwd',
                'cbdefghijklnrtuv',
                'cbdefghijklnrtuvcbdefghijklnrtuv'
            ],
            'Mixed case OTP is lowercased' => [
                'ddddddbTBHNHCJNKCFEIEGRRnnednjclUULDUerelthv',
                'ddddddbtbhnhcjnkcfeiegrrnnednjcluulduerelthv',
                '',
                'ddddddbtbhnh',
                'cjnkcfeiegrrnnednjcluulduerelthv'
            ],
        ];
    }

    /**
     * @dataProvider otpStrings
     */
    public function testItParsesFromString(string $string, string $otpString, string $password, string $publicId, string $cipherText): void
    {
        $otp = YubikeyOtp::fromString($string);

        $this->assertSame($otpString, $otp->otp);
        $this->assertSame($password, $otp->password);
        $this->assertSame($publicId, $otp->publicId);
        $this->assertSame($cipherText, $otp->cipherText);
    }

    /**
     * @dataProvider otpStrings
     */
    public function testItValidatesCorrectOtps(string $string): void
    {
        $this->assertTrue(YubikeyOtp::isValid($string));
    }

    /**
     * @dataProvider nonStrings
     */
    public function testItThrowsAnExceptionWhenGivenArgumentIsNotAString(mixed $nonString): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('not a string');

        YubikeyOtp::fromString($nonString);
    }

    /**
     * @return array
     */
    public function nonStrings(): array
    {
        return [
            'integer' => [1],
            'float' => [1.1],
            'array' => [[]],
            'object' => [new \stdClass],
            'null' => [null],
            'boolean' => [false],
        ];
    }

    /**
     * @dataProvider nonOtpStrings
     */
    public function testItThrowsAnExceptionWhenGivenStringIsNotAnOtpString(mixed $nonOtpString): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('not a valid OTP');

        YubikeyOtp::fromString($nonOtpString);
    }

    /**
     * @dataProvider nonOtpStrings
     */
    public function testItDoesntAcceptInvalidOtps(string $string): void
    {
        $this->assertFalse(YubikeyOtp::isValid($string));
    }

    public function nonOtpStrings(): array
    {
        return [
            'Has invalid characters' => ['abcdefghijklmnopqrstuvwxyz123456789'],
            'Too long' => [str_repeat('c', 100)],
            'Too short' => [str_repeat('c', 31)],
        ];
    }
}
