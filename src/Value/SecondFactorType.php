<?php

declare(strict_types = 1);

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
use Stringable;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;

final class SecondFactorType implements JsonSerializable, Stringable
{
    private readonly string $type;

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

    public function equals(self $other): bool
    {
        return $this->type === $other->type;
    }

    public function isSms(): bool
    {
        return $this->type === 'sms';
    }

    public function isYubikey(): bool
    {
        return $this->type === 'yubikey';
    }

    /**
     * @deprecated u2f support is removed from StepUp in favour of the WebAuthn GSSP
     */
    public function isU2f(): bool
    {
        return $this->type === 'u2f';
    }

    public function getSecondFactorType(): string
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): mixed
    {
        return $this->type;
    }
}
