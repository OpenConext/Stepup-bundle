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

namespace Surfnet\StepupBundle\Service\SmsSecondFactor;

use DateInterval;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Security\OtpGenerator;
use Surfnet\StepupBundle\Service\Exception\TooManyChallengesRequestedException;

final class SmsVerificationState
{
    /**
     * The maximum amount of attempts can be made, per OTP, to verify the OTP.
     */
    public const MAXIMUM_VERIFICATION_ATTEMPTS = 10;

    private readonly int $maximumOtpRequests;

    /**
     * @var Otp[]
     */
    private array $otps;

    private readonly int $verificationAttemptsMade;

    public function __construct(private readonly DateInterval $expiryInterval, int $maximumOtpRequests)
    {
        if ($maximumOtpRequests <= 0) {
            throw new InvalidArgumentException('Expected greater-than-zero number of maximum OTP requests.');
        }
        $this->maximumOtpRequests= $maximumOtpRequests;
        $this->otps = [];
        $this->verificationAttemptsMade = 0;
    }

    public function requestNewOtp(string $phoneNumber): string
    {
        if (count($this->otps) >= $this->maximumOtpRequests) {
            throw new TooManyChallengesRequestedException(
                sprintf(
                    '%d OTPs were requested, while only %d requests are allowed',
                    count($this->otps) + 1,
                    $this->maximumOtpRequests
                )
            );
        }

        $this->otps = array_filter($this->otps, fn(Otp $otp): bool => $otp->hasPhoneNumber($phoneNumber));

        $otp = OtpGenerator::generate(8);
        $this->otps[] = Otp::create($otp, $phoneNumber, $this->expiryInterval);

        return $otp;
    }

    public function verify(string $userOtp): OtpVerification
    {
        if ($this->verificationAttemptsMade >= self::MAXIMUM_VERIFICATION_ATTEMPTS) {
            return OtpVerification::tooManyAttempts();
        }

        $this->verificationAttemptsMade++;

        foreach ($this->otps as $otp) {
            $verification = $otp->verify($userOtp);

            if ($verification->didOtpMatch()) {
                return $verification;
            }
        }

        return OtpVerification::noMatch();
    }

    public function getOtpRequestsRemainingCount(): int
    {
        return $this->maximumOtpRequests - count($this->otps);
    }
}
