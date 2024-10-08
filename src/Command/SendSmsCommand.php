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

use Symfony\Component\Validator\Constraints as Assert;

class SendSmsCommand
{
    /**
     * @Assert\NotBlank(message="stepup.send_sms_command.recipient.may_not_be_empty")
     * @Assert\Type(type="string", message="stepup.send_sms_command.recipient.must_be_string")
     * @Assert\Regex(pattern="~^\d+$~", message="stepup.send_sms_command.recipient.must_consist_of_digits")
     *
     * The recipient as a string of digits (31612345678 for +31 6 1234 5678).
     *
     * @var string
     */
    public $recipient;

    /**
     * @var string
     */
    public $originator;

    /**
     * @var string
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
