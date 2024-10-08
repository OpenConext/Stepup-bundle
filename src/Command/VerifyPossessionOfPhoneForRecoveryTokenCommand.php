<?php

declare(strict_types = 1);

/**
 * Copyright 2022 SURFnet bv
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

class VerifyPossessionOfPhoneForRecoveryTokenCommand implements VerifyPossessionOfPhoneCommandInterface
{
    /**
     * @Assert\NotBlank(message="stepup.verify_possession_of_phone_command.challenge.may_not_be_empty")
     * @Assert\Type(type="string", message="stepup.verify_possession_of_phone_command.challenge.must_be_string")
     *
     * @var string
     */
    public $challenge;

    /**
     * @Assert\Type(type="string", message="stepup.verify_possession_of_phone_command.recovery_token_id.must_be_string")
     * @var string
     */
    public $recoveryTokenId;
}
