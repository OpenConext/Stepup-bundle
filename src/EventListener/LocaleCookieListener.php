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

use Psr\Log\LoggerInterface;
use Surfnet\StepupBundle\Http\CookieHelper;
use Surfnet\StepupBundle\Service\LocaleProviderService;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

final class LocaleCookieListener
{
    public function __construct(
        private readonly CookieHelper $cookieHelper,
        private readonly LocaleProviderService $localeProvider,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * If there is a logged in user with a preferred language, set it as a cookie.
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $locale = $this->localeProvider->determinePreferredLocale();

        // Unable to determine preferred locale? No need to hand out a cookie then.
        if (empty($locale)) {
            return;
        }

        // Did the request already contain the proper cookie value? No need to hand out a cookie then.
        $requestCookie = $this->cookieHelper->read($event->getRequest());
        if ($requestCookie && $requestCookie->getValue() === $locale) {
            $this->logger->debug(sprintf(
                'Locale cookie already set to "%s", nothing to do here',
                $locale
            ));
            return;
        }

        $this->cookieHelper->write($event->getResponse(), $locale);
        $this->logger->notice(sprintf("Set locale cookie to %s", $locale));
    }
}
