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
use http\Env\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionSmsVerificationStateHandler implements SmsVerificationStateHandler
{
    private readonly DateInterval $otpExpiryInterval;

    private SessionInterface $session;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly string $sessionKey,
        int $otpExpiryInterval,
        private readonly int $otpRequestMaximum
    ) {
        $this->session = $this->requestStack->getSession();
        $this->otpExpiryInterval = new DateInterval(sprintf('PT%dS', $otpExpiryInterval));
    }

    private function sessionKeyFrom(string $secondFactorId): string
    {
        return sprintf("%s_%s", $this->sessionKey, $secondFactorId);
    }

    public function hasState(string $secondFactorId): bool
    {
        return $this->session->has($this->sessionKeyFrom($secondFactorId));
    }

    public function clearState(string $secondFactorId): void
    {
        $this->session->remove($this->sessionKeyFrom($secondFactorId));
    }

    public function requestNewOtp(string $phoneNumber, string $secondFactorId): string
    {
        /** @var SmsVerificationState|null $state */
        $state = $this->session->get($this->sessionKeyFrom($secondFactorId));

        if ($state === null) {
            $state = new SmsVerificationState($this->otpExpiryInterval, $this->otpRequestMaximum);
            $this->session->set($this->sessionKeyFrom($secondFactorId), $state);
        }

        return $state->requestNewOtp($phoneNumber);
    }

    public function getOtpRequestsRemainingCount(string $secondFactorId): int
    {
        /** @var SmsVerificationState|null $state */
        $state = $this->session->get($this->sessionKeyFrom($secondFactorId));

        return $state !== null ? $state->getOtpRequestsRemainingCount() : $this->otpRequestMaximum;
    }

    public function getMaximumOtpRequestsCount(): int
    {
        return $this->otpRequestMaximum;
    }

    public function verify(string $otp, string $secondFactorId): OtpVerification
    {
        /** @var SmsVerificationState|null $state */
        $state = $this->session->get($this->sessionKeyFrom($secondFactorId));

        if ($state === null) {
            return OtpVerification::matchExpired();
        }

        $verification = $state->verify($otp);

        if ($verification->wasSuccessful()) {
            $this->session->remove($this->sessionKey);
        }

        return $verification;
    }
}
