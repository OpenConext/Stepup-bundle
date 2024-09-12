<?php

declare(strict_types = 1);

/**
 * Copyright 2018 SURFnet bv
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

namespace Surfnet\StepupBundle\Value\Provider;

use Surfnet\StepupBundle\Exception\InvalidArgumentException;

/**
 * Collection of ViewConfig instances. The collection can be used to find ViewConfig objects based on their second
 * factor type id.
 */
class ViewConfigCollection
{
    /**
     * @var ViewConfigInterface[]
     */
    private array $collection = [];

    public function addViewConfig(ViewConfigInterface $viewConfig, string $identifier): void
    {
        $this->collection[$identifier] = $viewConfig;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByIdentifier(string $identifier): ViewConfigInterface
    {
        if (isset($this->collection[$identifier])) {
            return $this->collection[$identifier];
        }
        throw new InvalidArgumentException(
            sprintf(
                'The provider identified by "%s" can not be found in the ViewConfigCollection',
                $identifier
            )
        );
    }

    public function isGssp(?string $identifier): bool
    {
        return isset($this->collection[$identifier]);
    }
}
