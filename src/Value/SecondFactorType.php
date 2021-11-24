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
use Surfnet\StepupBundle\Exception\InvalidArgumentException;

final class SecondFactorType implements JsonSerializable
{
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
        $this->type = $type;
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
     * @deprecated u2f support is removed from StepUp in favour of the WebAuthn GSSP
     */
    public function isU2f()
    {
        return $this->type === 'u2f';
    }

    /**
     * @return string
     */
    public function getSecondFactorType()
    {
        return $this->type;
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
