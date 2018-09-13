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

namespace Surfnet\StepupBundle\Tests\TestDouble\Service;

use Surfnet\StepupBundle\Command\SendSmsChallengeCommand;
use Surfnet\StepupBundle\Command\VerifyPossessionOfPhoneCommand;
use Surfnet\StepupBundle\Service\SmsSecondFactor\OtpVerification;
use Surfnet\StepupBundle\Service\SmsSecondFactor\SmsVerificationStateHandler;
use Surfnet\StepupBundle\Service\SmsSecondFactorServiceInterface;

class SmsSecondFactorService implements SmsSecondFactorServiceInterface
{
    /**
     * @var \Surfnet\StepupBundle\Service\SmsSecondFactor\SmsVerificationStateHandler
     */
    private $smsVerificationStateHandler;

    /**
     * @param SmsVerificationStateHandler $smsVerificationStateHandler
     */
    public function __construct(SmsVerificationStateHandler $smsVerificationStateHandler)
    {
        $this->smsVerificationStateHandler = $smsVerificationStateHandler;
    }

    /**
     * @return int
     */
    public function getOtpRequestsRemainingCount()
    {
        return 3;
    }

    /**
     * @return int
     */
    public function getMaximumOtpRequestsCount()
    {
        return 3;
    }

    /**
     * @return bool
     */
    public function hasSmsVerificationState()
    {
        return false;
    }

    public function clearSmsVerificationState()
    {
        // NOOP
    }

    /**
     * @param SendSmsChallengeCommand $command
     * @return bool
     */
    public function sendChallenge(SendSmsChallengeCommand $command)
    {
        return true;
    }

    /**
     * @param VerifyPossessionOfPhoneCommand $command
     * @return OtpVerification
     */
    public function verifyPossession(VerifyPossessionOfPhoneCommand $command)
    {
        return OtpVerification::foundMatch('+31 (0) 612345678');
    }
}
