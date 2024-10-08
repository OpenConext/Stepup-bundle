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

namespace Surfnet\StepupBundle\Value;

use Surfnet\StepupBundle\Exception\InvalidArgumentException;

class YubikeyOtp
{
    final public const OTP_REGEXP_QWERTY = '/^((.*):)?(([cbdefghijklnrtuv]{0,16})([cbdefghijklnrtuv]{32}))$/i';
    final public const OTP_REGEXP_DVORAK = '/^((.*):)?(([jxe\.uidchtnbpygk]{0,16})([jxe\.uidchtnbpygk]{32}))$/i';

    /** @var string */
    public $otp;

    /** @var string */
    public $password;

    /** @var string */
    public $publicId;

    /** @var string */
    public $cipherText;

    /**
     * @param string $string
     * @throws InvalidArgumentException Thrown when the given string is not an OTP.
     */
    public static function fromString($string): self
    {
        $otp = new self;

        if (!is_string($string)) {
            throw new InvalidArgumentException('Given OTP is not a string.');
        }

        $matches = [];
        if (preg_match(self::OTP_REGEXP_QWERTY, $string, $matches)) {
            $otp->otp = strtolower($matches[3]);
            $otp->password = $matches[2];
            $otp->publicId = strtolower($matches[4]);
            $otp->cipherText = strtolower($matches[5]);
        } elseif (preg_match(self::OTP_REGEXP_DVORAK, $string, $matches)) {
            $otp->otp = strtr(strtolower($matches[3]), 'jxe.uidchtnbpygk', 'cbdefghijklnrtuv');
            $otp->password = $matches[2];
            $otp->publicId = strtr(strtolower($matches[4]), 'jxe.uidchtnbpygk', 'cbdefghijklnrtuv');
            $otp->cipherText = strtr(strtolower($matches[5]), 'jxe.uidchtnbpygk', 'cbdefghijklnrtuv');
        } else {
            throw new InvalidArgumentException('Given string is not a valid OTP.');
        }

        return $otp;
    }

    /**
     * @param string $string
     */
    public static function isValid($string): bool
    {
        $matches = [];
        return preg_match(self::OTP_REGEXP_QWERTY, $string, $matches)
            || preg_match(self::OTP_REGEXP_DVORAK, $string, $matches);
    }
}
