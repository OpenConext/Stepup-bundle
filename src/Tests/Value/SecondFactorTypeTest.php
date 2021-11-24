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

namespace Surfnet\StepupBundle\Tests\Value;

use PHPUnit\Framework\TestCase ;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Value\SecondFactorType;

final class SecondFactorTypeTest extends TestCase
{
    public function validTypes()
    {
        return [
            'sms' => ['sms'],
            'tiqr' => ['tiqr'],
            'yubikey' => ['yubikey'],
            'biometric' => ['biometric'],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider validTypes
     *
     * @param string $type
     */
    public function it_can_be_constructed($type)
    {
        $secondFactorType = new SecondFactorType($type);

        $this->assertInstanceOf(SecondFactorType::class, $secondFactorType);
    }

    /**
     * @test
     * @group value
     */
    public function it_doesnt_accept_integers()
    {
        $this->expectException(InvalidArgumentException::class);

        new SecondFactorType(9);
    }

    /**
     * @test
     */
    public function its_equality_is_determined_by_its_type()
    {
        $this->assertTrue((new SecondFactorType('sms'))->equals(new SecondFactorType('sms')));
    }

    /**
     * @test
     */
    public function its_type_can_be_verified()
    {
        $this->assertTrue((new SecondFactorType('sms'))->isSms());
        $this->assertTrue((new SecondFactorType('yubikey'))->isYubikey());
    }
}
