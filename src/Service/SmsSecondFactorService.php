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

namespace Surfnet\StepupBundle\Service;

use Surfnet\StepupBundle\Command\SendSmsChallengeCommand;
use Surfnet\StepupBundle\Command\SendSmsCommand;
use Surfnet\StepupBundle\Command\VerifyPossessionOfPhoneCommand;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Service\SmsSecondFactor\OtpVerification;
use Surfnet\StepupBundle\Service\SmsSecondFactor\SmsVerificationStateHandler;

class SmsSecondFactorService implements SmsSecondFactorServiceInterface
{
    private string $originator;

    /**
     * @param string                      $originator
     */
    public function __construct(
        private readonly SmsService $smsService,
        private readonly SmsVerificationStateHandler $smsVerificationStateHandler,
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
        $this->originator = $originator;
    }

    public function getOtpRequestsRemainingCount(string $secondFactorId): int
    {
        return $this->smsVerificationStateHandler->getOtpRequestsRemainingCount($secondFactorId);
    }

    public function getMaximumOtpRequestsCount(): int
    {
        return $this->smsVerificationStateHandler->getMaximumOtpRequestsCount();
    }

    public function hasSmsVerificationState(string $secondFactorId): bool
    {
        return $this->smsVerificationStateHandler->hasState($secondFactorId);
    }

    public function clearSmsVerificationState(string $secondFactorId): void
    {
        $this->smsVerificationStateHandler->clearState($secondFactorId);
    }

    public function sendChallenge(SendSmsChallengeCommand $command): bool
    {
        $challenge = $this->smsVerificationStateHandler->requestNewOtp(
            (string) $command->phoneNumber,
            $command->secondFactorId
        )
        ;

        $smsCommand = new SendSmsCommand();
        $smsCommand->recipient = $command->phoneNumber->toMSISDN();
        $smsCommand->originator = $this->originator;
        $smsCommand->body = str_replace('%challenge%', $challenge, $command->body);
        $smsCommand->identity = $command->identity;
        $smsCommand->institution = $command->institution;

        return $this->smsService->sendSms($smsCommand);
    }

    public function verifyPossession(VerifyPossessionOfPhoneCommand $command): OtpVerification
    {
        return $this->smsVerificationStateHandler->verify($command->challenge, $command->secondFactorId);
    }
}
