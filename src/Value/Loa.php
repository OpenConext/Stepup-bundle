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
    const LOA_1 = 1.0;
    const LOA_SELF_VETTED = 1.5;
    const LOA_2 = 2.0;
    const LOA_3 = 3.0;

    /**
     * @var float
     */
    private $level;

    /**
     * @var string
     */
    private $identifier;

    public function __construct(float $level, string $identifier)
    {
        $possibleLevels = [self::LOA_1, self::LOA_SELF_VETTED, self::LOA_2, self::LOA_3];
        if (!in_array($level, $possibleLevels, true)) {
            throw new DomainException(sprintf(
                'Unknown loa level "%d", known levels: "%s"',
                $level,
                implode('", "', $possibleLevels)
            ));
        }

        if (!is_string($identifier)) {
            throw InvalidArgumentException::invalidType('string', 'identifier', $identifier);
        }

        $this->level = $level;
        $this->identifier = $identifier;
    }

    public function isIdentifiedBy(string $identifier): bool
    {
        return $this->identifier === $identifier;
    }

    public function levelIsLowerOrEqualTo(float $level): bool
    {
        return $this->level <= $level;
    }

    public function levelIsHigherOrEqualTo(float $level): bool
    {
        return $this->level >= $level;
    }

    public function canSatisfyLoa(Loa $loa): bool
    {
        return $loa->levelIsLowerOrEqualTo($this->level);
    }

    public function equals(Loa $loa): bool
    {
        return $this->level === $loa->level
            && $this->identifier === $loa->identifier;
    }

    public function isOfLevel(float $loaLevel): bool
    {
        return $this->level === $loaLevel;
    }

    public function getLevel(): float
    {
        return $this->level;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
