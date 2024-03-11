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

namespace Surfnet\StepupBundle\Http;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Read and write a given cookie from HTTP Requests/Responses.
 */
final class CookieHelper
{
    public function __construct(private Cookie $cookieSettings)
    {
    }

    /**
     * Write a new value for the current cookie to a given Response.
     *
     * @param string $value
     */
    public function write(Response $response, $value): Cookie
    {
        $cookie = $this->createCookieWithValue($value);
        $response->headers->setCookie($cookie);
        return $cookie;
    }

    /**
     * Retrieve the current cookie from the Request if it exists.
     *
     * Note that we only read the value, we ignore the other settings.
     *
     */
    public function read(Request $request): ?Cookie
    {
        if (!$request->cookies->has($this->cookieSettings->getName())) {
            return null;
        }

        return $this->createCookieWithValue(
            $request->cookies->get($this->cookieSettings->getName())
        );
    }

    /**
     * Create a new cookie from the current (template) cookie with a new value.
     *
     */
    private function createCookieWithValue($value): Cookie
    {
        return new Cookie(
            $this->cookieSettings->getName(),
            $value,
            $this->cookieSettings->getExpiresTime(),
            $this->cookieSettings->getPath(),
            $this->cookieSettings->getDomain(),
            $this->cookieSettings->isSecure(),
            $this->cookieSettings->isHttpOnly()
        );
    }
}
