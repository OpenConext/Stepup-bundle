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

namespace Surfnet\StepupBundle\Service;


use Surfnet\StepupBundle\Exception\DomainException;
use Surfnet\StepupBundle\Value\GssfConfig;
use Surfnet\StepupBundle\Value\Loa;
use Surfnet\StepupBundle\Value\SecondFactorType;
use Surfnet\StepupBundle\Value\VettingType;
use function array_key_exists;
use function in_array;

class SecondFactorTypeService
{
    /**
     * @var array
     */
    private $loaLevelTypeMap = [
        'sms' => Loa::LOA_2,
        'yubikey' => Loa::LOA_3,
    ];

    /**
     * When the vetting type is of a certain type, a LoA subtraction might apply
     *
     * Rationale being that a token has a certain strength. For example a Yubikey
     * is a stronger token than say a SMS based token. The same principle applies
     * for the vetting type. An on-premise vetting action gives a high level of
     * authenticity, where a self-asserted registration tells us far less about the
     * Identity.
     *
     * @var array
     */
    private $vettingTypeSubtractions = [
        VettingType::TYPE_SELF_ASSERTED_REGISTRATION => Loa::LOA_SELF_VETTED
    ];

    /**
     * @var GssfConfig
     */
    private $gssfConfig;

    /**
     * @param array $gssfConfig
     */
    public function __construct(array $gssfConfig)
    {
        $this->gssfConfig = new GssfConfig($gssfConfig);
    }

    /**
     * @return string[]
     */
    public function getAvailableSecondFactorTypes()
    {
        return array_merge(
            $this->getAvailableGssfSecondFactorTypes(),
            array_keys($this->loaLevelTypeMap)
        );
    }

    /**
     * @return string[]
     */
    private function getAvailableGssfSecondFactorTypes()
    {
        return $this->gssfConfig->getSecondFactorTypes();
    }

    public function canSatisfy(SecondFactorType $secondFactorType, Loa $loa, VettingType $vettingType): bool
    {
        return $loa->levelIsLowerOrEqualTo($this->getLevel($secondFactorType, $vettingType));
    }

    public function isSatisfiedBy(SecondFactorType $secondFactorType, Loa $loa, VettingType $vettingType): bool
    {
        return $loa->levelIsHigherOrEqualTo($this->getLevel($secondFactorType, $vettingType));
    }

    public function hasEqualOrHigherLoaComparedTo(
        SecondFactorType $secondFactorType,
        VettingType $vettingType,
        SecondFactorType $other,
        VettingType $otherVettingType
    ): bool {
        return $this->getLevel($secondFactorType, $vettingType) >= $this->getLevel($other, $otherVettingType);
    }

    public function hasEqualOrLowerLoaComparedTo(
        SecondFactorType $secondFactorType,
        VettingType $vettingType,
        SecondFactorType $other,
        VettingType $otherVettingType
    ): bool {
        return $this->getLevel($secondFactorType, $vettingType) <= $this->getLevel($other, $otherVettingType);
    }

    public function getLevel(SecondFactorType $secondFactorType, VettingType $vettingType): float
    {
        // For now the substraction model works as follows: is your vetting type in the
        // list of subtractions? Then the LoA level set for that vetting type is applied
        if (array_key_exists($vettingType->getType(), $this->vettingTypeSubtractions)) {
            return $this->vettingTypeSubtractions[$vettingType->getType()];
        }

        $loaMap = array_merge(
            $this->loaLevelTypeMap,
            $this->gssfConfig->getLoaMap()
        );

        if (array_key_exists($secondFactorType->getSecondFactorType(), $loaMap)) {
            return $loaMap[$secondFactorType->getSecondFactorType()];
        }
        throw new DomainException(
            sprintf(
                'The Loa level of this type: %s can\'t be retrieved.',
                $secondFactorType->getSecondFactorType()
            )
        );
    }

    public function isGssf(SecondFactorType $secondFactorType)
    {
        return in_array($secondFactorType->__toString(), $this->getAvailableGssfSecondFactorTypes());
    }
}
