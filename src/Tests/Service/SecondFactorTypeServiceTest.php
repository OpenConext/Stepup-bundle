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
use Surfnet\StepupBundle\Value\VettingType;

class SecondFactorTypeServiceTest extends TestCase
{
    private $vettingTypeSelfAsserted;
    private $vettingTypeOnPremise;

    protected function setUp(): void
    {
        $this->vettingTypeOnPremise = new VettingType(VettingType::TYPE_ON_PREMISE);
        $this->vettingTypeSelfAsserted = new VettingType(VettingType::TYPE_SELF_ASSERTED_REGISTRATION);
    }

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
    }

    /**
     * @group service
     */
    public function testGetLevel()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $this->assertEquals(2, $service->getLevel(new SecondFactorType('sms'), $this->vettingTypeOnPremise));
    }

    /**
     * @group service
     */
    public function testGetLevelSubtractedOnSelfAssertedRegistration()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $this->assertEquals(1.5, $service->getLevel(new SecondFactorType('sms'), $this->vettingTypeSelfAsserted));
    }

    /**
     * @group service
     */
    public function testGetLevelCannotGetLevelOfNonExistingSecondFactorType()
    {
        $this->expectExceptionMessage("The Loa level of this type: u3f can't be retrieved.");
        $this->expectException(\Surfnet\StepupBundle\Exception\DomainException::class);

        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $service->getLevel(new SecondFactorType('u3f'), $this->vettingTypeOnPremise);
    }

    /**
     * @group service
     */
    public function testItRejectsInvalidVettingType()
    {
        $this->expectExceptionMessage('The provided vetting type "self-righteous-registration" is not permitted. Use one of on-premise, self-asserted-registration, self-vet, unknown');
        $this->expectException(\Surfnet\StepupBundle\Exception\InvalidArgumentException::class);

        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $service->getLevel(new SecondFactorType('yubikey'), new VettingType('self-righteous-registration'));
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
    }

    /**
     * @group service
     */
    public function testItCanTestForSatisfactoryLoaLevel()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $loa1 = new Loa(1, 'level-1');
        $loa15 = new Loa(1.5, 'level-1-5');
        $loa2 = new Loa(2, 'level-2');
        $loa3 = new Loa(3, 'level-3');
        $yubikey = new SecondFactorType('yubikey');
        $tiqr = new SecondFactorType('tiqr');
        $sms = new SecondFactorType('sms');
        $biometric = new SecondFactorType('biometric');

        $this->assertTrue($service->canSatisfy($yubikey, $loa1, $this->vettingTypeOnPremise));
        $this->assertTrue($service->canSatisfy($yubikey, $loa15, $this->vettingTypeOnPremise));
        $this->assertTrue($service->canSatisfy($sms, $loa2, $this->vettingTypeOnPremise));
        $this->assertTrue($service->canSatisfy($tiqr, $loa3, $this->vettingTypeOnPremise));
        $this->assertTrue($service->canSatisfy($biometric, $loa3, $this->vettingTypeOnPremise));
        $this->assertFalse($service->canSatisfy($sms, $loa3, $this->vettingTypeOnPremise));
        $this->assertFalse($service->canSatisfy($sms, $loa2, $this->vettingTypeSelfAsserted));
        $this->assertFalse($service->canSatisfy($yubikey, $loa2, $this->vettingTypeSelfAsserted));
    }

    /**
     * @group service
     */
    public function testIsSatisfiedBy()
    {
        $service = new SecondFactorTypeService($this->getAvailableSecondFactorTypes());
        $loa1 = new Loa(1, 'level-1');
        $loa15 = new Loa(1.5, 'level-1-5');
        $loa2 = new Loa(2, 'level-2');
        $loa3 = new Loa(3, 'level-3');
        $yubikey = new SecondFactorType('yubikey');
        $sms = new SecondFactorType('sms');

        $this->assertFalse($service->isSatisfiedBy($yubikey, $loa1, $this->vettingTypeOnPremise));
        $this->assertFalse($service->isSatisfiedBy($yubikey, $loa2, $this->vettingTypeOnPremise));
        $this->assertTrue($service->isSatisfiedBy($yubikey, $loa3, $this->vettingTypeOnPremise));
        $this->assertTrue($service->isSatisfiedBy($sms, $loa2, $this->vettingTypeOnPremise));
        $this->assertTrue($service->isSatisfiedBy($sms, $loa3, $this->vettingTypeOnPremise));

        $this->assertFalse($service->isSatisfiedBy($yubikey, $loa1, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->isSatisfiedBy($yubikey, $loa2, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->isSatisfiedBy($yubikey, $loa3, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->isSatisfiedBy($sms, $loa2, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->isSatisfiedBy($sms, $loa3, $this->vettingTypeSelfAsserted));
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

        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $biometric, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $sms, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $yubikey, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($yubikey, $this->vettingTypeOnPremise, $sms, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($sms, $this->vettingTypeOnPremise, $sms, $this->vettingTypeOnPremise));

        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $biometric, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $sms, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $yubikey, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($yubikey, $this->vettingTypeOnPremise, $sms, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($sms, $this->vettingTypeOnPremise, $sms, $this->vettingTypeSelfAsserted));

        $this->assertFalse($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $biometric, $this->vettingTypeOnPremise));
        $this->assertFalse($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeOnPremise));
        $this->assertFalse($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $yubikey, $this->vettingTypeOnPremise));
        $this->assertFalse($service->hasEqualOrHigherLoaComparedTo($yubikey, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeOnPremise));
        $this->assertFalse($service->hasEqualOrHigherLoaComparedTo($sms, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeOnPremise));

        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $biometric, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $yubikey, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($yubikey, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrHigherLoaComparedTo($sms, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeSelfAsserted));
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

        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $biometric, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($sms, $this->vettingTypeOnPremise, $tiqr, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($sms, $this->vettingTypeOnPremise, $biometric, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($yubikey, $this->vettingTypeOnPremise, $biometric, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($sms, $this->vettingTypeOnPremise, $sms, $this->vettingTypeOnPremise));

        $this->assertFalse($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $biometric, $this->vettingTypeSelfAsserted));
        $this->assertFalse($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $sms, $this->vettingTypeSelfAsserted));
        $this->assertFalse($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeOnPremise, $yubikey, $this->vettingTypeSelfAsserted));
        $this->assertFalse($service->hasEqualOrLowerLoaComparedTo($yubikey, $this->vettingTypeOnPremise, $sms, $this->vettingTypeSelfAsserted));
        $this->assertFalse($service->hasEqualOrLowerLoaComparedTo($sms, $this->vettingTypeOnPremise, $sms, $this->vettingTypeSelfAsserted));

        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $biometric, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $yubikey, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($yubikey, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeOnPremise));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($sms, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeOnPremise));

        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $biometric, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($tiqr, $this->vettingTypeSelfAsserted, $yubikey, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($yubikey, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeSelfAsserted));
        $this->assertTrue($service->hasEqualOrLowerLoaComparedTo($sms, $this->vettingTypeSelfAsserted, $sms, $this->vettingTypeSelfAsserted));
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
