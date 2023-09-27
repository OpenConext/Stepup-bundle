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

final class Country implements \Stringable
{
    public $name;
    private readonly string $countryName;

    /**
     * @param string $countryName
     */
    public function __construct(private readonly CountryCode $countryCode, $countryName)
    {
        if (!is_string($countryName)) {
            throw InvalidArgumentException::invalidType('string', 'countryName', $countryName);
        }
        $this->countryName = $countryName;
    }

    /**
     * @return CountryCode
     */
    public function getCountryCode(): \Surfnet\StepupBundle\Value\PhoneNumber\CountryCode
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getCountryName(): string
    {
        return $this->countryName;
    }

    /**
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->countryName === $other->name && $this->countryCode->equals($other->countryCode);
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->countryName, $this->countryCode);
    }
}
