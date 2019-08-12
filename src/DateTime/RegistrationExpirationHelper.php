<?php

/**
 * Copyright 2018 SURFnet bv
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

namespace Surfnet\StepupBundle\DateTime;

use DateInterval;
use DateTime as CoreDateTime;
use Exception;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;

/**
 * Helps with testing if a registration date of a token is within the expiration time frame.
 */
class RegistrationExpirationHelper
{
    /**
     * The current time, this field can be set for testing purposes
     * @var DateTime
     */
    private $now;

    /**
     * @var string a DateInterval complient $interval_spec string
     */
    private $expirationWindow;

    public function __construct(CoreDateTime $now = null, $expirationWindow = 'P14D')
    {
        $this->now = $now;

        try {
            $this->expirationWindow = new DateInterval($expirationWindow);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                sprintf(
                    'The provided DateInterval interval specification ("%s") is invalid',
                    $expirationWindow
                )
            );
        }
    }

    public function expiresAt(CoreDateTime $registeredAt)
    {
        $registrationDate = clone $registeredAt;
        return $registrationDate->add($this->expirationWindow);
    }

    public function hasExpired(CoreDateTime $registeredAt)
    {
        $now = $this->getNow();
        return $this->expiresAt($registeredAt) <= $now;
    }

    private function getNow()
    {
        if (is_null($this->now)) {
            $this->now = new CoreDateTime();
        }
        return $this->now;
    }
}
