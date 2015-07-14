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

namespace Surfnet\StepupBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CacheControlHeaderResponseListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $responseHeaders = $event->getResponse()->headers;

        $responseHeaders->addCacheControlDirective('max-age', 0);
        $responseHeaders->addCacheControlDirective('private', true);
        $responseHeaders->addCacheControlDirective('no-cache', true);
        $responseHeaders->addCacheControlDirective('no-store', true);
    }
}
