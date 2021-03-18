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

namespace Surfnet\StepupBundle\DependencyInjection\Configurator;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Mockery as m;
use PHPUnit\Framework\TestCase as UnitTest;
use Surfnet\StepupBundle\Guzzle\Middleware\GuzzleRequestIdInjector;

class GuzzleClientRequestIdConfiguratorTest extends UnitTest
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @group Configurator
     * @group Guzzle
     */
    public function testTheRequestIdInjectorIsAttachedToTheGuzzleClient()
    {
        $requestIdInjector = m::mock(GuzzleRequestIdInjector::class);
        $guzzleClientRequestIdConfigurator = new GuzzleClientRequestIdConfigurator($requestIdInjector);

        $handlerStack = m::mock(HandlerStack::class)
            ->shouldReceive('push')
            ->once()
            ->withArgs([$requestIdInjector])
            ->getMock();
        $client = m::mock(Client::class)
            ->shouldReceive('getConfig')
            ->once()
            ->withArgs(['handler'])
            ->andReturn($handlerStack)
            ->getMock();

        $guzzleClientRequestIdConfigurator->configure($client);
    }
}
