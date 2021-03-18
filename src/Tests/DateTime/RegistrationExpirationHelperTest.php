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

namespace Surfnet\StepupBundle\Tests\DateTime;

use DateTime;
use PHPUnit\Framework\TestCase as UnitTest;
use Surfnet\StepupBundle\DateTime\RegistrationExpirationHelper;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;

class RegistrationExpirationHelperTest extends UnitTest
{
    public function test_expires_at_is_fourteen_days_in_future()
    {
        $requestedAt = new DateTime('2000-01-01');

        $expectedExpirationDate = new DateTime('2000-01-15');
        $expectedRequestedAtData = new DateTime('2000-01-01');

        $helper = $this->buildHelper($requestedAt);

        $this->assertEquals($expectedExpirationDate, $helper->expiresAt($requestedAt));
        $this->assertEquals(
            $expectedRequestedAtData,
            $requestedAt,
            'The registrationRequestedAt date was changed during operations on the helper. This field should not have changed.'
        );
    }

    public function test_expires_at_is_configurable()
    {
        $requestedAt = new DateTime('2000-01-01');

        $expectedExpirationDate = new DateTime('2001-01-01');
        $expectedRequestedAtData = new DateTime('2000-01-01');

        $helper = $this->buildHelper($requestedAt, 'P1Y');

        $this->assertEquals($expectedExpirationDate, $helper->expiresAt($requestedAt));
        $this->assertEquals(
            $expectedRequestedAtData,
            $requestedAt,
            'The registrationRequestedAt date was changed during operations on the helper. This field should not have changed.'
        );
    }

    public function test_has_expired()
    {
        $requestedAt = new DateTime('2000-01-01');
        $now = new DateTime('2018-04-12');

        $helper = $this->buildHelper($now);

        $this->assertTrue($helper->hasExpired($requestedAt));
    }

    public function test_has_not_expired()
    {
        $requestedAt = new DateTime('2000-01-01 00:00:00');
        $now = new DateTime('2000-01-14 23:59:59');

        $helper = $this->buildHelper($now);

        $this->assertFalse($helper->hasExpired($requestedAt));
    }

    public function test_invalid_interval_is_rejected()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The provided DateInterval interval specification ("D41P") is invalid');

        $this->buildHelper(null, 'D41P');
    }

    private function buildHelper(DateTime $date = null, $window = 'P14D')
    {
        return new RegistrationExpirationHelper($date, $window);
    }
}
