<?php

declare(strict_types = 1);

/**
 * Copyright 2017 SURFnet B.V.
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

namespace Surfnet\StepupBundle\Guzzle\Middleware;

use Psr\Http\Message\RequestInterface;
use Surfnet\StepupBundle\EventListener\RequestIdRequestResponseListener;
use Surfnet\StepupBundle\Request\RequestId;

/**
 * Middleware injecting the Stepup Request Id in every Guzzle request.
 */
class GuzzleRequestIdInjector
{
    public function __construct(private readonly RequestId $requestId)
    {
    }

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader(RequestIdRequestResponseListener::HEADER_NAME, $this->requestId->get());
            return $handler($request, $options);
        };
    }
}
