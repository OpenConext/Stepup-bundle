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

namespace Surfnet\StepupBundle\Value;

use Surfnet\StepupBundle\Exception\InvalidArgumentException;

final class YubikeyPublicId implements \Stringable
{
    private readonly string $value;

    public static function fromOtp(YubikeyOtp $otp)
    {
        $hexadecimalId = strtr($otp->publicId, 'cbdefghijklnrtuv', '0123456789abcdef');
        $gmpId = gmp_init($hexadecimalId, 16);

        return new self(sprintf('%08s', gmp_strval($gmpId, 10)));
    }

    public function __construct($value)
    {
        if (!is_string($value)) {
            throw InvalidArgumentException::invalidType('string', 'value', $value);
        }

        // Numeric IDs must be left-padded with zeroes until eight characters. Longer IDs may not be padded.
        if (!preg_match('~^\d+$~', $value)) {
            throw new InvalidArgumentException('Given Yubikey public ID is not a string of digits');
        }
        if ($value !== sprintf('%08s', ltrim($value, '0'))) {
            throw new InvalidArgumentException(
                'Given Yubikey public ID is longer than 8 digits, yet left-padded with zeroes'
            );
        }

        // Yubikey public IDs, in their (mod)hex format, may be up to sixteen characters in length. Thus, this is their
        // maximum value.
        if (gmp_cmp(gmp_init($value, 10), gmp_init('ffffffffffffffff', 16)) > 0) {
            throw new InvalidArgumentException('Given Yubikey public ID is larger than 0xffffffffffffffff');
        }

        $this->value = $value;
    }

    public function getYubikeyPublicId()
    {
        return $this->value;
    }

    public function equals(YubikeyPublicId $other)
    {
        return $this->value === $other->value;
    }

    public function jsonSerialize()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
