<?php

/**
 * Copyright 2017 SURFnet bv
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

namespace Surfnet\StepupBundle\Tests\Service;

use PHPUnit\Framework\TestCase ;
use Surfnet\StepupBundle\Service\SecondFactorTypeService;
use Surfnet\StepupBundle\Value\Loa;
use Surfnet\StepupBundle\Value\SecondFactorType;

class SecondFactorTypeServiceTest extends TestCase
{
    /**
     * @group service
     */
    public function testItCanBeCreated()
    {
        $service = new SecondFactorTypeService([]);
        $this->assertInstanceOf(SecondFactorTypeService::class, $service);
    }

    /**
     * @group service
     */
    public function testItCanBeAskedForEnabledSecondFactorTypes()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $types = $service->getAvailableSecondFactorTypes();
        $this->assertIsArray($types);
        $this->assertContains('tiqr', $types);
        $this->assertContains('biometric', $types);
        $this->assertContains('sms', $types);
        $this->assertContains('yubikey', $types);
        $this->assertContains('u2f', $types);
    }

    /**
     * @group service
     */
    public function testGetLevel()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $this->assertEquals(3, $service->getLevel(new SecondFactorType('u2f')));
        $this->assertEquals(2, $service->getLevel(new SecondFactorType('sms')));
    }

    /**
     * @group service
     */
    public function testGetLevelCannotGetLevelOfNonExistingSecondFactorType()
    {
        $this->expectExceptionMessage("The Loa level of this type: u3f can't be retrieved.");
        $this->expectException(\Surfnet\StepupBundle\Exception\DomainException::class);

        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $service->getLevel(new SecondFactorType('u3f'));
    }

    /**
     * @group service
     */
    public function testItCanBeAskedForEnabledSecondFactorTypesWhenNoGssfSet()
    {
        $service = new SecondFactorTypeService([]);
        $types = $service->getAvailableSecondFactorTypes();

        $this->assertIsArray($types);
        $this->assertContains('sms', $types);
        $this->assertContains('yubikey', $types);
        $this->assertContains('u2f', $types);
    }

    /**
     * @group service
     */
    public function testItCanTestForSatisfactoryLoaLevel()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $loa1 = new Loa(1, 'level-1');
        $loa2 = new Loa(2, 'level-2');
        $loa3 = new Loa(3, 'level-3');
        $yubikey = new SecondFactorType('yubikey');
        $tiqr = new SecondFactorType('tiqr');
        $sms = new SecondFactorType('sms');
        $biometric = new SecondFactorType('biometric');

        $this->assertTrue($service->canSatisfy($yubikey, $loa1));
        $this->assertTrue($service->canSatisfy($tiqr, $loa3));
        $this->assertTrue($service->canSatisfy($biometric, $loa3));
        $this->assertFalse($service->canSatisfy($sms, $loa3));
        $this->assertTrue($service->canSatisfy($sms, $loa2));
    }

    /**
     * @group service
     */
    public function testIsSatisfiedBy()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $loa1 = new Loa(1, 'level-1');
        $loa2 = new Loa(2, 'level-2');
        $loa3 = new Loa(3, 'level-3');
        $yubikey = new SecondFactorType('yubikey');
        $sms = new SecondFactorType('sms');

        $this->assertFalse($service->isSatisfiedBy($yubikey, $loa1));
        $this->assertFalse($service->isSatisfiedBy($yubikey, $loa2));
        $this->assertTrue($service->isSatisfiedBy($yubikey, $loa3));
        $this->assertTrue($service->isSatisfiedBy($sms, $loa2));
        $this->assertTrue($service->isSatisfiedBy($sms, $loa3));
    }

    /**
     * @group service
     */
    public function testHasEqualOrHigherLoaComparedTo()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $yubikey = new SecondFactorType('yubikey');
        $tiqr = new SecondFactorType('tiqr');
        $sms = new SecondFactorType('sms');
        $biometric = new SecondFactorType('biometric');

        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $biometric));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $sms));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $yubikey));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($yubikey, $sms));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($sms, $sms));
    }

    /**
     * @group service
     */
    public function testHasEqualOrLowerLoaComparedTo()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $yubikey = new SecondFactorType('yubikey');
        $tiqr = new SecondFactorType('tiqr');
        $sms = new SecondFactorType('sms');
        $biometric = new SecondFactorType('biometric');

        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($tiqr, $biometric));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($sms, $tiqr));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($sms, $biometric));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($yubikey, $biometric));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($sms, $sms));
    }

    /**
     * @group service
     */
    public function testItCanDetermineSecondFactorTypeIsGssf()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());

        $yubikey = new SecondFactorType('yubikey');
        $tiqr = new SecondFactorType('tiqr');
        $bogus = new SecondFactorType('i-dont-exist');

        $this->assertTrue($service->isGssf($tiqr), 'Expected Tiqr to be Gssf');
        $this->assertFalse($service->isGssf($yubikey), 'Expected Yubikey not to be Gssf');
        $this->assertFalse($service->isGssf($bogus), 'Expected Bogus token not to be Gssf');
    }

    private function getAvailableSecondFactorTypes()
    {
        return [
            'biometric' => [
                'loa' => 3
            ],
            'tiqr' => [
                'loa' => 3
            ],
        ];
    }
}