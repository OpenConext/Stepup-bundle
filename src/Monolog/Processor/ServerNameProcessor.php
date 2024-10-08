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

namespace Surfnet\StepupBundle\Monolog\Processor;

use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ServerNameProcessor
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * @return array
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return $record;
        }

        $serverName = $request->server->get('SERVER_NAME') ?: $request->server->get('SERVER_ADDR');

        if ($serverName === null) {
            return $record;
        }

        $record->extra['server'] = $serverName;

        return $record;
    }
}
