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

namespace Mibew\Asset\Generator;

use Symfony\Component\HttpFoundation\Request;

class UrlGenerator implements UrlGeneratorInterface
{
    protected $scheme;
    protected $host;
    protected $httpPort;
    protected $httpsPort;
    protected $basePath;

    /**
     * The constructor.
     *
     * @param string $host The HTTP host name without port and scheme.
     * @param string $base_path The base path.
     * @param string $scheme The scheme.
     * @param int $http_port The port which is used for HTTP requests.
     * @param int $https_port The port which is used for HTTPS requests.
     */
    public function __construct($host = 'localhost', $base_path = '', $scheme = 'http', $http_port = 80, $https_port = 443)
    {
        $this->scheme = strtolower($scheme);
        $this->host = $host;
        $this->httpPort = $http_port;
        $this->httpsPort = $https_port;
        $this->basePath = $base_path;
    }

    /**
     * Sets all needed values from the request.
     *
     * @param Request $request A request to get values from.
     */
    public function setRequest(Request $request)
    {
        $this->setScheme($request->getScheme());
        $this->setHost($request->getHost());
        $this->setBasePath($request->getBasePath());
        if ($request->isSecure()) {
            $this->setHttpsPort($request->getPort());
        } else {
            $this->setHttpPort($request->getPort());
        }
    }

    /**
     * Gets the scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Sets the scheme.
     *
     * @param string $scheme
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Gets the host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the host.
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Gets the HTTP port.
     *
     * @return int
     */
    public function getHttpPort()
    {
        return $this->httpPort;
    }

    /**
     * Sets the HTTP port.
     *
     * @param int $port
     */
    public function setHttpPort($port)
    {
        $this->httpPort = $port;
    }

    /**
     * Gets the HTTPS port.
     *
     * @return int
     */
    public function getHttpsPort()
    {
        return $this->httpsPort;
    }

    /**
     * Sets the HTTPS port.
     *
     * @param int $port
     */
    public function setHttpsPort($port)
    {
        $this->httpsPort = $port;
    }

    /**
     * Gets the base path.
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Sets the base path.
     *
     * @param string $base_path
     */
    public function setBasePath($base_path)
    {
        $this->basePath = $base_path;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($relative_path, $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->doGenerate($relative_path, $reference_type, false);
    }

    /**
     * {@inheritdoc}
     */
    public function generateSecure($relative_path, $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->doGenerate($relative_path, $reference_type, true);
    }

    protected function doGenerate($relative_path, $reference_type, $force_secure)
    {
        $scheme = $force_secure ? 'https' : $this->getScheme();
        $host = '';
        $port = false;

        // Check if a non-standard port is used.
        if ($scheme == 'http' && $this->getHttpPort() != 80) {
            $port = $this->getHttpPort();
        } elseif ($scheme == 'https' && $this->getHttpsPort() != 443) {
            $port = $this->getHttpsPort();
        }

        $need_host =
            // A user wants an absolute URL
            ($reference_type === UrlGeneratorInterface::ABSOLUTE_URL)
                // A scheme deffers from one from request.
                || $scheme !== $this->getScheme()
                // A non-standard port is used.
                || $port;

        if ($need_host) {
            $host = $scheme . '://' . $this->getHost();

            if ($port) {
                $host .= ':' . $port;
            }
        }

        // Make sure path componets are properly encoded.
        $path_parts = explode('/', $relative_path);
        $encoded_path = implode('/', array_map('rawurlencode', $path_parts));

        return $host . $this->getBasePath() . '/' . ltrim($encoded_path, '/');
    }
}
