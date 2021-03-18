<?php

/**
 * Copyright 2016 SURFnet bv
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

namespace Surfnet\StepupBundle\Guzzle\Subscriber;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase as UnitTest;
use Surfnet\StepupBundle\EventListener\RequestIdRequestResponseListener;
use Surfnet\StepupBundle\Guzzle\Middleware\GuzzleRequestIdInjector;
use Surfnet\StepupBundle\Request\RequestId;
use Surfnet\StepupBundle\Request\RequestIdGenerator;

class GuzzleRequestIdInjectorTest extends UnitTest
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @group Guzzle
     */
    public function testItSetsTheRequestIdAsHeader()
    {
        $expectedRequestId = 'my-request-id';

        $requestId = new RequestId(m::mock(RequestIdGenerator::class));
        $requestId->set($expectedRequestId);

        $history = [];
        $historyMiddleware  = Middleware::history($history);
        $injector = new GuzzleRequestIdInjector($requestId);
        $mockHandler = new MockHandler([new Response(200)]);

        $handlerStack = HandlerStack::create($mockHandler);
        $handlerStack->push($injector);
        $handlerStack->push($historyMiddleware);

        $client = new Client(['handler' => $handlerStack]);

        $client->request('GET', '/');

        /** @var Request $pastRequest */
        $pastRequest = $history[0]['request'];
        $actualRequestId = $pastRequest->getHeaderLine(RequestIdRequestResponseListener::HEADER_NAME);

        $this->assertSame(
            $expectedRequestId,
            $actualRequestId,
            sprintf(
                'RequestId header ("%s") is expected to be injected with value "%s", but it was not (received: "%s")',
                RequestIdRequestResponseListener::HEADER_NAME,
                $expectedRequestId,
                $actualRequestId
            )
        );
    }
}
