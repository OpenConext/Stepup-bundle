<?php

/**
 * Copyright 2022 SURFnet bv
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

use Surfnet\StepupBundle\Exception\InvalidArgumentException;

final class VettingType
{
    public const TYPE_ON_PREMISE = 'on-premise';
    public const TYPE_SELF_VET = 'self-vet';
    public const TYPE_SELF_ASSERTED_REGISTRATION = 'self-asserted-registration';
    public const TYPE_UNKNOWN = 'unknown';

    private static $allowedTypes = [
        self::TYPE_ON_PREMISE,
        self::TYPE_SELF_ASSERTED_REGISTRATION,
        self::TYPE_SELF_VET,
        self::TYPE_UNKNOWN
    ];

    /**
     * @var string
     */
    private $type;

    public function __construct(string $type)
    {
        if (!in_array($type, self::$allowedTypes)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The provided vetting type "%s" is not permitted. Use one of %s',
                    $type,
                    implode(', ', self::$allowedTypes)
                )
            );
        }
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
