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

namespace Mibew\Routing;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Symfony\Component\Routing\Router as BaseRouter;

class Router extends BaseRouter implements RouterInterface
{
    /**
     * {@inheritdoc}
     *
     * The only difference from
     * {@link \Symfony\Component\Routing\Router::setOptions()} is in default
     * values.
     */
    public function setOptions(array $options)
    {
        $this->options = array(
            'cache_dir'              => null,
            'debug'                  => false,
            'generator_class'        => 'Mibew\\Routing\\Generator\\UrlGenerator',
            'generator_base_class'   => 'Mibew\\Routing\\Generator\\UrlGenerator',
            'generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'generator_cache_class'  => 'MibewUrlGenerator',
            'matcher_class'          => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_base_class'     => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_dumper_class'   => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'matcher_cache_class'    => 'MibewUrlMatcher',
            'resource_type'          => null,
            'strict_requirements'    => true,
        );

        // Check option names and live merge, if errors are encountered
        // Exception will be thrown
        $invalid = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new \InvalidArgumentException(sprintf(
                'The Router does not support the following options: "%s".',
                implode('", "', $invalid)
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        if (null === $this->collection) {
            $collection = $this->loader->load($this->resource, $this->options['resource_type']);

            // Add an ability for plugins to alter routes list
            $arguments = array('routes' => $collection);
            EventDispatcher::getInstance()->triggerEvent(Events::ROUTES_ALTER, $arguments);

            $this->collection = $arguments['routes'];
        }

        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function generateSecure($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        return $this->getGenerator()->generateSecure($name, $parameters, $referenceType);
    }
}
