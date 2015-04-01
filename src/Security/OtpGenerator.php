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

use Surfnet\StepupBundle\Exception\LogicException;
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
    const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Securely generate a 8-character OTP containing only characters from the OtpGenerator::ALPHABET constant.
     * Based on https://gist.github.com/pmeulen/3dff8bab3227ed340dd1
     *
     * @return string
     * @throws OtpGenerationRuntimeException
     */
    public static function generate()
    {
        $passwordLength = 8; // The length of the password to generate
        $bitsPerValue = 5;  // The 32 possible characters in the Base32 alphabet can be represented in exactly 5 bits
        $randomBytesRequired = (integer) (($passwordLength * $bitsPerValue) / 8) + 1;
        $cryptoStrong = false;
        $randomBytes = openssl_random_pseudo_bytes($randomBytesRequired, $cryptoStrong); // Generate random bytes

        if ($cryptoStrong === false) {
            throw new OtpGenerationRuntimeException('openssl_random_pseudo_bytes() is not cryptographically strong');
        }

        if ($randomBytes === false) {
            throw new OtpGenerationRuntimeException('openssl_random_pseudo_bytes() failed');
        }

        if (strlen($randomBytes) < $randomBytesRequired) {
            throw new LogicException('Logic error');
        }

        // Transform each byte $random_bytes into $random_bits where each byte
        // is converted to its 8 character ASCII binary representation.
        // This allows us to work with the individual bits using the php string functions
        // Not very efficient, but easy to understand.
        $randomBits = '';
        for ($i = 0; $i < $randomBytesRequired; ++$i) {
            $randomBits .= str_pad(decbin(ord($randomBytes[$i])), 8, '0', STR_PAD_LEFT);
        }

        // Get 'bits' form $random_bits string in blocks of 5 bits, convert bits to value [0..32> and use
        // this as offset in self::ALPHABET to pick the character
        $password = '';
        for ($i = 0; $i < $passwordLength; ++$i) {
            $randomValueBin = substr($randomBits, $i * $bitsPerValue, $bitsPerValue);

            if (strlen($randomValueBin) < $bitsPerValue) {
                throw new LogicException('Logic error');
            }

            $password .= substr(self::ALPHABET, bindec($randomValueBin), 1);
        }

        return $password;
    }
}
