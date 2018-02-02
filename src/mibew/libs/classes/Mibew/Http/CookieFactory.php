<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
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

namespace Mibew\Http;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

/**
 * Creates cookie without need to know anything about domain, path and
 * connection type.
 */
class CookieFactory
{
    /**
     * A path at the server a cookie will be created for.
     *
     * @var string
     */
    protected $path;

    /**
     * A domain a cookie will be created for.
     *
     * @var string|null
     */
    protected $domain;

    /**
     * Indicates if a cookie available only for https connections.
     *
     * @var bool
     */
    protected $secure;

    /**
     * Creates a factory using incoming request.
     *
     * @param Request $request Incoming request.
     * @return CookieFactory
     */
    public static function fromRequest(Request $request)
    {
        return new self($request->getBasePath(), $request->getHost(), $request->isSecure());
    }

    /**
     * Class counstructor.
     *
     * Set values that will be used for all created cookies.
     *
     * @param string $path A path at the server a cookie will be created for.
     * @param string|null $domain A domain a cookie will be created for.
     * @param bool $secure Indicates if a cookie available only for https
     *   connections.
     */
    public function __construct($path = '/', $domain = null, $secure = false)
    {
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
    }

    /**
     * Creates a cookie object.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int|string|\DateTime $expire The time the cookie expires.
     * @param bool $httpOnly Whether the cookie will be made accessible only
     *   through the HTTP protocol.
     * @return Cookie
     */
    public function createCookie($name, $value = null, $expire = 0, $http_only = true)
    {
        return new Cookie(
            $name,
            $value,
            $expire,
            $this->getPath(),
            $this->getDomain(),
            $this->isSecure(),
            $http_only
        );
    }

    /**
     * Returns cookie's path associated with the factory.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns cookie's domain associated with the factory.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Returns cookie's secure flag value associated with the factory.
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure;
    }
}
