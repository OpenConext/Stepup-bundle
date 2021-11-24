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

    /**
     * @param SecondFactorType $secondFactorType
     * @param Loa $loa
     * @return bool
     */
    public function canSatisfy(SecondFactorType $secondFactorType, Loa $loa)
    {
        return $loa->levelIsLowerOrEqualTo($this->getLevel($secondFactorType));
    }

    /**
     * @param SecondFactorType $secondFactorType
     * @param Loa $loa
     * @return bool
     */
    public function isSatisfiedBy(SecondFactorType $secondFactorType, Loa $loa)
    {
        return $loa->levelIsHigherOrEqualTo($this->getLevel($secondFactorType));
    }

    /**
     * @param SecondFactorType $secondFactorType
     * @param SecondFactorTypeService|SecondFactorType $other
     * @return bool
     */
    public function hasEqualOrHigherLoaComparedTo(SecondFactorType $secondFactorType, SecondFactorType $other)
    {
        return $this->getLevel($secondFactorType) >= $this->getLevel($other);
    }

    /**
     * @param SecondFactorType $secondFactorType
     * @param SecondFactorTypeService|SecondFactorType $other
     * @return bool
     */
    public function hasEqualOrLowerLoaComparedTo(SecondFactorType $secondFactorType, SecondFactorType $other)
    {
        return $this->getLevel($secondFactorType) <= $this->getLevel($other);
    }

    /**
     * @param SecondFactorType $secondFactorType
     * @return int
     */
    public function getLevel(SecondFactorType $secondFactorType)
    {
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
