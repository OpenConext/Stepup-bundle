<?php

declare(strict_types = 1);

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

namespace Surfnet\StepupBundle\Value;

class GssfConfig
{
    public function __construct(private readonly array $config = [])
    {
    }

    /**
     * @return array
     */
    public function getSecondFactorTypes(): array
    {
        return array_keys($this->config);
    }

    /**
     * Flattens the config and returns key value pairs where the key is the SF type and the value is the LOA level
     * @return mixed[]
     */
    public function getLoaMap(): array
    {
        $loaMap = [];
        foreach ($this->config as $key => $config) {
            if (array_key_exists('loa', $config)) {
                $loaMap[$key] = $config['loa'];
            }
        }
        return $loaMap;
    }
}
