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

namespace Surfnet\StepupBundle\Command;

use Surfnet\StepupBundle\Value\PhoneNumber\InternationalPhoneNumber;

class SendSmsChallengeCommand implements SendSmsChallengeCommandInterface
{
    /**
     * @var InternationalPhoneNumber
     */
    public $phoneNumber;

    /**
     * @var string
     */
    public $secondFactorId;

    /**
     * @var string The SMS contents. '%challenge%' will be replaced with the generated OTP.
     */
    public $body;

    /**
     * The requesting identity's ID (not name ID).
     *
     * @var string
     */
    public $identity;

    /**
     * The requesting identity's institution.
     *
     * @var string
     */
    public $institution;
}
