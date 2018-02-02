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

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * UrlGenerator can generate a secure URL or a path for any route in the
 * RouteCollection based on the passed parameters.
 */
class UrlGenerator extends BaseUrlGenerator implements SecureUrlGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateSecure($name, $parameters = array(), $reference_type = self::ABSOLUTE_PATH)
    {
        if (null === $route = $this->routes->get($name)) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        // The Route has a cache of its own and is not recompiled as long as it
        // does not get modified.
        $compiled_route = $route->compile();

        // Force a route to use HTTPS for this time.
        $requirements = $route->getRequirements();
        $requirements['_scheme'] = 'https';

        return $this->doGenerate(
            $compiled_route->getVariables(),
            $route->getDefaults(),
            $requirements,
            $compiled_route->getTokens(),
            $parameters,
            $name,
            $reference_type,
            $compiled_route->getHostTokens()
        );
    }
}
