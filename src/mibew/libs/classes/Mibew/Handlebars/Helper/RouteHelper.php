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

namespace Mibew\Handlebars\Helper;

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\Template;
use Mibew\Routing\RouterAwareInterface;
use Mibew\Routing\RouterInterface;

/**
 * A helper that generates url based on route name and parameters list.
 *
 * Example of usage:
 * <code>
 *   {{route "hello" to="world"}}
 * </code>
 * The code above generates URL for route named "hello" and pass parameter
 * "to" equals to "world" to URL generator.
 */
class RouteHelper implements HelperInterface
{
    /**
     * @var RouterAwareInterface
     */
    protected $routerContainer = null;

    /**
     * Helper's constructor.
     *
     * @param RouterAwareInterface $router_container An object that keeps router
     * instance.
     */
    public function __construct(RouterAwareInterface $router_container)
    {
        $this->routerContainer = $router_container;
    }

    /**
     * {@inheritdoc}
     *
     * @todo Use combined arguments parser when it will be implemented in
     *   Handlebars.php.
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $named_args = $template->parseNamedArguments($args);
        $positional_args = $template->parseArguments($args);
        $route_name = (string)$context->get($positional_args[0]);

        $parameters = array();
        foreach ($named_args as $name => $parsed_arg) {
            $parameters[$name] = $context->get($parsed_arg);
        }

        return $this->getRouter()->generate($route_name, $parameters);
    }

    /**
     * Extracts router from the router's container related with the object.
     *
     * @return RouterInterface
     */
    protected function getRouter()
    {
        return $this->routerContainer->getRouter();
    }
}
