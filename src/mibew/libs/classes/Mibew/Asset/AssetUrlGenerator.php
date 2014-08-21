<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2014 the original author or authors.
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

namespace Mibew\Asset;

use Symfony\Component\HttpFoundation\Request;

class AssetUrlGenerator implements AssetUrlGeneratorInterface
{

    protected $scheme;
    protected $host;
    protected $port;
    protected $basePath;

    /**
     * The constructor.
     *
     * @param string $host The HTTP host name without port and scheme.
     * @param string $base_path The base path.
     * @param string $scheme The scheme.
     * @param int $port The port.
     */
    public function __construct($host = 'localhost', $base_path = '', $scheme = 'http', $port = 80)
    {
        $this->scheme = strtolower($scheme);
        $this->host = $host;
        $this->port = $port;
        $this->basePath = $base_path;
    }

    /**
     * Sets all needed values according to the request.
     *
     * @param Request $request A request to get values from.
     */
    public function fromRequest(Request $request)
    {
        $this->setScheme($request->getScheme());
        $this->setHost($request->getHost());
        $this->setPort($request->getPort());
        $this->setBasePath($request->getBasePath());
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
     * Gets the port.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the port.
     *
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
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
    public function generate($relative_path, $reference_type = AssetUrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $host = '';
        $need_port = ($this->getScheme() == 'http' && $this->getPort() != 80)
            || ($this->getScheme() == 'https' && $this->getPort() != 443);

        if ($need_port || $reference_type === AssetUrlGeneratorInterface::ABSOLUTE_URL) {
            $host = $this->getScheme() . '://' . $this->getHost();

            if ($need_port) {
                // A non standatd port is used. It should be added to the
                // resulting URL.
                $host .= ':' . $this->getPort();
            }
        }

        // Make sure path componets are properly encoded.
        $path_parts = explode('/', $relative_path);
        $encoded_path = implode('/', array_map('rawurlencode', $path_parts));

        return $host . $this->getBasePath() . '/' . ltrim($encoded_path, '/');
    }
}
