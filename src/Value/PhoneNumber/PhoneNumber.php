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
use Surfnet\StepupBundle\Value\Exception\InvalidPhoneNumberFormat;

class PhoneNumber
{
    /**
     * @var string
     */
    private $number;

    /**
     * @param string $number
     */
    public function __construct($number)
    {
        if (!is_string($number)) {
            throw InvalidArgumentException::invalidType('string', 'number', $number);
        }

        if (!preg_match('~^\d+$~', $number)) {
            throw new InvalidPhoneNumberFormat($number);
        }

        $this->number = $number;
    }

    /**
     * Returns the part of the MSISN formatted as representing the [NDC|NPA] + SN part
     * @see http://en.wikipedia.org/wiki/MSISDN#MSISDN_Format
     *
     * @return string
     */
    public function formatAsMsisdnPart()
    {
        $number = $this->number;
        // we may only strip a single leading zero
        if (strpos($number, '0') === 0) {
            $number = substr($number, 1);
        }

        return $number;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param PhoneNumber $other
     * @return bool
     */
    public function equals(PhoneNumber $other)
    {
        return $this->formatAsMsisdnPart() === $other->formatAsMsisdnPart();
    }

    public function __toString()
    {
        return '(0) ' . $this->formatAsMsisdnPart();
    }
}
