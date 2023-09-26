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

use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Security\Exception\OtpGenerationRuntimeException;

/**
 * A stand-alone class for securely generating OTPs.
 */
final class OtpGenerator
{
    /**
     * The characters used in the OTP. Must be a power of two characters long to ensure all characters have equal chance
     * of being included.
     */
    public const CHARACTER_SET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * The 32 possible characters in the Base32 alphabet can be represented in exactly 5 bits
     */
    public const BITS_PER_CHARACTER = 5;

    /**
     * Securely generate an arbitrary length OTP containing only characters from the OtpGenerator::CHARACTER_SET constant.
     * Based on https://gist.github.com/pmeulen/3dff8bab3227ed340dd1
     *
     * @param int $length The length of the OTP to generate
     * @return string
     * @throws OtpGenerationRuntimeException
     */
    public static function generate($length)
    {
        if (!is_int($length)) {
            throw InvalidArgumentException::invalidType('int', 'length', $length);
        }

        if ($length <= 0) {
            throw new InvalidArgumentException('Expected positive integer $length');
        }

        $bitsPerValue = self::BITS_PER_CHARACTER;
        $randomBytesRequired = (int) ceil($length * $bitsPerValue / 8);
        $cryptoStrong = false;
        $randomBytes = openssl_random_pseudo_bytes($randomBytesRequired, $cryptoStrong); // Generate random bytes

        if ($cryptoStrong === false) {
            throw new OtpGenerationRuntimeException('openssl_random_pseudo_bytes() is not cryptographically strong');
        }

        if ($randomBytes === false) {
            throw new OtpGenerationRuntimeException('openssl_random_pseudo_bytes() failed');
        }

        // Transform each byte $random_bytes into $random_bits where each byte
        // is converted to its 8 character ASCII binary representation.
        // This allows us to work with the individual bits using the php string functions
        // Not very efficient, but easy to understand.
        $randomBits = '';
        for ($i = 0; $i < $randomBytesRequired; $i++) {
            $randomBits .= str_pad(decbin(ord($randomBytes[$i])), 8, '0', STR_PAD_LEFT);
        }

        // Get 'bits' form $random_bits string in blocks of 5 bits, convert bits to value [0..32> and use
        // this as offset in self::CHARACTER_SET to pick the character
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $randomValueBin = substr($randomBits, $i * $bitsPerValue, $bitsPerValue);

            $password .= substr(self::CHARACTER_SET, bindec($randomValueBin), 1);
        }

        return $password;
    }
}
