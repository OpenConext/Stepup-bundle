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

namespace Surfnet\StepupBundle\Service;

use Surfnet\StepupBundle\Command\SendSmsChallengeCommand;
use Surfnet\StepupBundle\Command\SendSmsCommand;
use Surfnet\StepupBundle\Command\VerifyPossessionOfPhoneCommand;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Service\SmsSecondFactor\OtpVerification;
use Surfnet\StepupBundle\Service\SmsSecondFactor\SmsVerificationStateHandler;

class SmsSecondFactorService implements SmsSecondFactorServiceInterface
{
    /**
     * @var \Surfnet\StepupBundle\Service\SmsService
     */
    private $smsService;

    /**
     * @var \Surfnet\StepupBundle\Service\SmsSecondFactor\SmsVerificationStateHandler
     */
    private $smsVerificationStateHandler;

    /**
     * @var string
     */
    private $originator;

    /**
     * @param SmsService                  $smsService
     * @param SmsVerificationStateHandler $smsVerificationStateHandler
     * @param string                      $originator
     */
    public function __construct(
        SmsService $smsService,
        SmsVerificationStateHandler $smsVerificationStateHandler,
        $originator
    ) {
        if (!is_string($originator)) {
            throw InvalidArgumentException::invalidType('string', 'originator', $originator);
        }

        if (!preg_match('~^[a-z0-9]{1,11}$~i', $originator)) {
            throw new InvalidArgumentException(
                'Invalid SMS originator given: may only contain alphanumerical characters.'
            );
        }

        $this->smsService = $smsService;
        $this->smsVerificationStateHandler = $smsVerificationStateHandler;
        $this->originator = $originator;
    }

    /**
     * @return int
     */
    public function getOtpRequestsRemainingCount()
    {
        return $this->smsVerificationStateHandler->getOtpRequestsRemainingCount();
    }

    /**
     * @return int
     */
    public function getMaximumOtpRequestsCount()
    {
        return $this->smsVerificationStateHandler->getMaximumOtpRequestsCount();
    }

    /**
     * @return bool
     */
    public function hasSmsVerificationState()
    {
        return $this->smsVerificationStateHandler->hasState();
    }

    public function clearSmsVerificationState()
    {
        $this->smsVerificationStateHandler->clearState();
    }

    /**
     * @param SendSmsChallengeCommand $command
     * @return bool
     */
    public function sendChallenge(SendSmsChallengeCommand $command)
    {
        $challenge = $this->smsVerificationStateHandler->requestNewOtp((string) $command->phoneNumber);

        $smsCommand = new SendSmsCommand();
        $smsCommand->recipient = $command->phoneNumber->toMSISDN();
        $smsCommand->originator = $this->originator;
        $smsCommand->body = str_replace('%challenge%', $challenge, $command->body);
        $smsCommand->identity = $command->identity;
        $smsCommand->institution = $command->institution;

        return $this->smsService->sendSms($smsCommand);
    }

    /**
     * @param VerifyPossessionOfPhoneCommand $command
     * @return OtpVerification
     */
    public function verifyPossession(VerifyPossessionOfPhoneCommand $command)
    {
        return $this->smsVerificationStateHandler->verify($command->challenge);
    }
}
