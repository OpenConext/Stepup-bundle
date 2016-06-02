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

namespace Surfnet\StepupBundle\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

final class LocaleCookieService
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var bool
     */
    private $secure;

    /**
     * @var bool
     */
    private $httpOnly;

    /**
     * LanguageCookieService constructor.
     * @param string $name
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     */
    public function __construct($name, $domain, $secure, $httpOnly)
    {
        $this->name = $name;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * Set the given locale as a cookie on the given HTTP Response.
     *
     * @param string $locale
     *   Locale to set as cookie value.
     * @param Response $response
     *   HTTP response to add the cookie to.
     *
     * @return Response
     *   Augmented Response.
     */
    public function set($locale, Response $response)
    {
        if (empty($this->name)) {
            return $response;
        }

        $response->headers->setCookie(new Cookie($this->name, $locale, 0, '/', $this->domain, $this->secure, $this->httpOnly));

        return $response;
    }
}
