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

namespace Mibew\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Mibew\Routing\Generator\UrlGenerator;
use Mibew\Routing\RouteCollectionLoader;

class Router implements RouterInterface, RequestMatcherInterface
{
    /**
     * @var UrlMatcher|null
     */
    protected $matcher = null;

    /**
     * @var UrlGenerator|null
     */
    protected $generator = null;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var RouteCollection|null
     */
    protected $collection = null;

    /**
     * @var RouteCollectionLoader|null
     */
    protected $loader = null;

    /**
     * @var array Router's options.
     */
    protected $options = array();

    /**
     * Class constructor.
     *
     * @param RouteLoader $loader An instance of route loader.
     * @param RequestContext $context The context of the request.
     */
    public function __construct(RouteCollectionLoader $loader, RequestContext $context = null, $options = array())
    {
        $this->context = $context ? $context : new RequestContext();
        $this->loader = $loader;
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        if (is_null($this->collection)) {
            $this->collection = $this->loader->load($this->getOption('route_collection'));
        }

        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        return $this->getGenerator()->generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function generateSecure($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        return $this->getGenerator()->generateSecure($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;

        // Update request context in URL matcher instance
        if (!is_null($this->matcher)) {
            $this->matcher->setContext($context);
        }

        // Update request context in URL generator instance
        if (!is_null($this->generator)) {
            $this->generator->setContext($context);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        return $this->getMatcher()->matchRequest($request);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        return $this->getMatcher()->match($pathinfo);
    }

    /**
     * Gets the UrlMatcher instance associated with this Router.
     *
     * @return UrlMatcher
     */
    public function getMatcher()
    {
        if (is_null($this->matcher)) {
            $this->matcher = new UrlMatcher($this->getRouteCollection(), $this->getContext());
        }

        return $this->matcher;
    }

    /**
     * Gets the UrlGenerator instance associated with this Router.
     *
     * @return UrlGenerator
     */
    public function getGenerator()
    {
        if (is_null($this->generator)) {
            $this->generator = new UrlGenerator($this->getRouteCollection(), $this->getContext());
        }

        return $this->generator;
    }

    /**
     * Sets all router's options.
     *
     * @param array $options List of options to set.
     * @throws \InvalidArgumentException If not supported option is found.
     */
    public function setOptions($options)
    {
        // Reset router's options to defaults.
        $this->options = array(
            'route_collection' => RouteCollectionLoader::ROUTES_ALL,
        );

        // Update options and find invalid ones.
        $invalid = array();
        foreach ($options as $name => $value) {
            if (array_key_exists($name, $this->options)) {
                $this->options[$name] = $value;
            } else {
                $invalid[] = $name;
            }
        }

        if (!empty($invalid)) {
            throw new \InvalidArgumentException(sprintf(
                'The Router does not support the following options: "%s".',
                implode('", "', $invalid)
            ));
        }
    }

    /**
     * Sets router's option to the specified value.
     *
     * @param string $name Option name.
     * @param string $value Option value.
     * @throws \InvalidArgumentException If the option is not supported.
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support "%s" option.', $name));
        }

        $this->options[$name] = $value;
    }

    /**
     * Gets router's option by its name.
     *
     * @param string $name Option name.
     * @return mixed Option value.
     * @throws \InvalidArgumentException If the option is not supported.
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support "%s" option.', $name));
        }

        return $this->options[$name];
    }
}
