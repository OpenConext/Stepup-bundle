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

use JsonSerializable;
use Surfnet\StepupBundle\Exception\DomainException;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods) All methods are relevant and simple.
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) All methods are relevant and simple.
 */
final class SecondFactorType implements JsonSerializable
{
    private static $loaLevelTypeMap = [
        'sms'       => Loa::LOA_2,
        'tiqr'      => Loa::LOA_2,
        'yubikey'   => Loa::LOA_3,
        'u2f'       => Loa::LOA_3,
        'biometric' => Loa::LOA_3,
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        if (!is_string($type)) {
            throw InvalidArgumentException::invalidType('string', 'type', $type);
        }

        if (!isset(self::$loaLevelTypeMap[$type])) {
            throw new DomainException(
                sprintf(
                    "Invalid second factor type, got '%s', expected one of '%s'",
                    $type,
                    join(',', array_keys(self::$loaLevelTypeMap))
                )
            );
        }

        $this->type = $type;
    }

    /**
     * @param Loa $loa
     * @return bool
     */
    public function canSatisfy(Loa $loa)
    {
        return $loa->levelIsLowerOrEqualTo($this->getLevel());
    }

    /**
     * @param Loa $loa
     * @return bool
     */
    public function isSatisfiedBy(Loa $loa)
    {
        return $loa->levelIsHigherOrEqualTo($this->getLevel());
    }

    /**
     * @param self $other
     * @return bool
     */
    public function hasEqualOrHigherLoaComparedTo(self $other)
    {
        return $this->getLevel() >= $other->getLevel();
    }

    /**
     * @param self $other
     * @return bool
     */
    public function hasEqualOrLowerLoaComparedTo(self $other)
    {
        return $this->getLevel() <= $other->getLevel();
    }

    /**
     * @param self $other
     * @return bool
     */
    public function equals(self $other)
    {
        return $this->type === $other->type;
    }

    /**
     * @return bool
     */
    public function isSms()
    {
        return $this->type === 'sms';
    }

    /**
     * @return bool
     */
    public function isYubikey()
    {
        return $this->type === 'yubikey';
    }

    /**
     * @return bool
     */
    public function isTiqr()
    {
        return $this->type === 'tiqr';
    }

    /**
     * @return bool
     */
    public function isU2f()
    {
        return $this->type === 'u2f';
    }

    /**
     * @return bool
     */
    public function isBiometric()
    {
        return $this->type === 'biometric';
    }

    /**
     * Returns whether this type is one of the Generic SAML Second Factor types.
     *
     * @return bool
     */
    public function isGssf()
    {
        return $this->type === 'tiqr' || $this->type === 'biometric';
    }

    /**
     * @return string
     */
    public function getSecondFactorType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return self::$loaLevelTypeMap[$this->type];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }

    public function jsonSerialize()
    {
        return $this->type;
    }
}
