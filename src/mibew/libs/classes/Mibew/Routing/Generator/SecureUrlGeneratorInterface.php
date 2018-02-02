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

namespace Mibew\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

interface SecureUrlGeneratorInterface extends UrlGeneratorInterface
{
    /**
     * Generates a URL or path for a specific route based on the given
     * parameters. Unlike {@link UrlGeneratorInterface::generate()} the URLs its
     * generate uses HTTPS no matter what scheme is used in a route defenition.
     *
     * @param string $name The name of the route
     * @param mixed $parameters An array of parameters
     * @param bool|string $referenceType The type of reference to be generated
     *   (one of the constants from {@link UrlGeneratorInterface}).
     *
     * @return string The generated URL
     *
     * @throws RouteNotFoundException If the named route doesn't exist
     * @throws MissingMandatoryParametersException When some parameters are
     *   missing that are mandatory for the route
     * @throws InvalidParameterException When a parameter value for a
     *   placeholder is not correct because it does not match the requirement
     */
    public function generateSecure($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH);
}
