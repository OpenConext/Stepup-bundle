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

namespace Surfnet\StepupBundle\Service;

use Surfnet\StepupBundle\Command\SendSmsChallengeCommand;
use Surfnet\StepupBundle\Command\VerifyPossessionOfPhoneCommand;
use Surfnet\StepupBundle\Service\SmsSecondFactor\OtpVerification;

interface SmsSecondFactorServiceInterface
{
    /**
     * The remaining number of requests as an integer value.
     * @return int
     */
    public function getOtpRequestsRemainingCount(string $secondFactorId): int;

    /**
     * Return the number of OTP requests that can be taken as an integer value.
     * @return int
     */
    public function getMaximumOtpRequestsCount(): int;

    /**
     * Tests if this session has made prior requests
     */
    public function hasSmsVerificationState(string $secondFactorId): bool;

    /**
     * Clears the verification state, forget this user has performed SMS requests.
     * @return mixed
     */
    public function clearSmsVerificationState(string $secondFactorId);

    /**
     * Send an SMS OTP challenge
     *
     * This challenge gets sent to the recipient whose information can be found in the SendSmsChallengeCommand.
     * This method will return a boolean which indicates if the challenge was sent successfully.
     *
     * When the MaximumOtpRequestsCount is reached, this method should throw the TooManyChallengesRequestedException
     */
    public function sendChallenge(SendSmsChallengeCommand $command): bool;

    /**
     * Verify the SMS OTP
     *
     * Proving possession by verifying the OTP, the recipient received and typed in a web form, matches the OTP that was
     * sent. Various results can be returned in the form of a ProofOfPossessionResult.
     */
    public function verifyPossession(VerifyPossessionOfPhoneCommand $command): OtpVerification;
}
