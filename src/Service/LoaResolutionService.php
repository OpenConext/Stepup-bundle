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

namespace Surfnet\StepupBundle\Service;

use LogicException;
use Surfnet\StepupBundle\Value\Loa;

class LoaResolutionService
{
    /**
     * @var Loa[]
     */
    private array $loas = [];

    public function __construct(array $loaDefinitions)
    {
        foreach ($loaDefinitions as $definition) {
            $this->addLoaDefinition($definition);
        }
    }

    public function hasLoa(string $loaIdentifier): bool
    {
        foreach ($this->loas as $loa) {
            if ($loa->isIdentifiedBy($loaIdentifier)) {
                return true;
            }
        }

        return false;
    }

    public function getLoa(string $loaIdentifier): ?Loa
    {
        foreach ($this->loas as $loa) {
            if ($loa->isIdentifiedBy($loaIdentifier)) {
                return $loa;
            }
        }

        return null;
    }

    public function getLoaByLevel(float $loaLevel): ?Loa
    {
        foreach ($this->loas as $loa) {
            if ($loa->isOfLevel($loaLevel)) {
                return $loa;
            }
        }

        return null;
    }

    private function addLoaDefinition(Loa $loa): void
    {
        foreach ($this->loas as $existingLoa) {
            if ($existingLoa->equals($loa)) {
                throw new LogicException(sprintf(
                    'Cannot add Loa "%s" as it has already been added',
                    $loa
                ));
            }
        }

        $this->loas[] = $loa;
    }
}
