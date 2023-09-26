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

namespace Surfnet\StepupBundle\Monolog\Processor;

use Exception;
use Surfnet\StepupBundle\Exception\Art;

class ArtProcessor
{
    /**
     * @return array
     */
    public function __invoke(array $record)
    {
        if (!isset($record['context']['exception'])) {
            return $record;
        }

        // Symfony 3 triggers this processor with SilencedErrorContext objects
        // in case of PHP errors (deprecation notices, errors, etc). We should
        // only generate Art codes for exceptions.
        if (!$record['context']['exception'] instanceof Exception) {
            return $record;
        }

        $record['extra']['art'] = Art::forException($record['context']['exception']);

        return $record;
    }
}
