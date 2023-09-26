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

namespace Surfnet\StepupBundle\Value\PhoneNumber;

use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Value\Exception\InvalidCountryCodeFormatException;
use Surfnet\StepupBundle\Value\Exception\UnknownCountryCodeException;

class CountryCode implements \Stringable
{
    private readonly string $countryCode;

    /**
     * @param string $countyCode
     */
    public function __construct($countyCode)
    {
        if (!is_string($countyCode)) {
            throw InvalidArgumentException::invalidType('string', 'countryCodeDefinition', $countyCode);
        }

        if (!preg_match('~^\d+$~', $countyCode)) {
            throw new InvalidCountryCodeFormatException($countyCode);
        }

        if (!CountryCodeListing::isValidCountryCode($countyCode)) {
            throw UnknownCountryCodeException::unknownCountryCode($countyCode);
        }

        $this->countryCode = $countyCode;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return bool
     */
    public function equals(CountryCode $other)
    {
        return $this->countryCode === $other->countryCode;
    }

    public function __toString(): string
    {
        $countryCode = $this->getCountryCode();

        // 4 digits (1234) are split after the first (1 234), same for both Kazakhstan codes after the first digit
        if (strlen($countryCode) === 4 || in_array($countryCode, ['77', '76'])) {
            $countryCode = substr($countryCode, 0, 1) . ' ' . substr($countryCode, 1);
        }

        return '+' . $countryCode;
    }
}
