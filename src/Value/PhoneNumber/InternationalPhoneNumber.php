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

namespace Surfnet\StepupBundle\Value\PhoneNumber;

use Stringable;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Value\Exception\InvalidPhoneNumberFormatException;

class InternationalPhoneNumber implements Stringable
{
    public function __construct(private readonly CountryCode $countryCode, private readonly PhoneNumber $phoneNumber)
    {
    }

    /**
     * @param string $string a well formatted "+{CountryCode} (0) {PhoneNumber}" international phone number
     */
    public static function fromStringFormat($string): self
    {
        if (!is_string($string)) {
            throw InvalidArgumentException::invalidType('string', 'string', $string);
        }

        $matches = [];
        if (!preg_match('~^\+([^\(]+)\(0\)\s{1}([\d]+)$~', $string, $matches)) {
            throw new InvalidPhoneNumberFormatException(
                'In order to create the phone number from string, it must have the format "+31 (0) 614696076", formal: '
                . '"+{CountryCode} (0) {PhoneNumber}"'
            );
        }

        $countryCode = str_replace(' ', '', $matches[1]);
        $phoneNumber = $matches[2];

        return new self(new CountryCode($countryCode), new PhoneNumber($phoneNumber));
    }

    /**
     * @see http://en.wikipedia.org/wiki/MSISDN#MSISDN_Format
     * @see https://www.messagebird.com/developers#messaging-send
     *
     */
    public function toMSISDN(): string
    {
        return $this->countryCode->getCountryCode() . $this->phoneNumber->formatAsMsisdnPart();
    }

    public function equals(InternationalPhoneNumber $other): bool
    {
        return $this->countryCode->equals($other->countryCode)
                && $this->phoneNumber->equals($other->phoneNumber);
    }

    public function __toString(): string
    {
        return $this->countryCode . ' ' . $this->phoneNumber;
    }
}
