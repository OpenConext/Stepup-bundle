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

namespace Surfnet\StepupBundle\Value;

use Surfnet\StepupBundle\Exception\DomainException;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;

/**
 * Value object representing the different LOAs that can be configured
 */
class Loa
{
    /**
     * The different levels
     */
    const LOA_1 = 1;
    const LOA_2 = 2;
    const LOA_3 = 3;

    /**
     * @var int
     */
    private $level;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @param int    $level
     * @param string $identifier
     */
    public function __construct($level, $identifier)
    {
        $possibleLevels = [self::LOA_1, self::LOA_2, self::LOA_3];
        if (!in_array($level, $possibleLevels, true)) {
            throw new DomainException(sprintf(
                'Unknown loa level "%s", known levels: "%s"',
                is_object($level) ? get_class($level) : $level,
                implode('", "', $possibleLevels)
            ));
        }

        if (!is_string($identifier)) {
            throw InvalidArgumentException::invalidType('string', 'identifier', $identifier);
        }

        $this->level = $level;
        $this->identifier = $identifier;
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function isIdentifiedBy($identifier)
    {
        return $this->identifier === $identifier;
    }

    /**
     * @param int $level
     * @return bool
     */
    public function levelIsLowerOrEqualTo($level)
    {
        return $this->level <= $level;
    }

    /**
     * @param int $level
     * @return bool
     */
    public function levelIsHigherOrEqualTo($level)
    {
        return $this->level >= $level;
    }

    /**
     * @param Loa $loa
     * @return bool
     */
    public function canSatisfyLoa(Loa $loa)
    {
        return $loa->levelIsLowerOrEqualTo($this->level);
    }

    /**
     * @param Loa $loa
     * @return bool
     */
    public function equals(Loa $loa)
    {
        return $this->level === $loa->level
            && $this->identifier === $loa->identifier;
    }

    /**
     * @param int $loaLevel
     * @return bool
     */
    public function isOfLevel($loaLevel)
    {
        return $this->level === $loaLevel;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function __toString()
    {
        return  $this->identifier;
    }
}
