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
use Handlebars\Template;

/**
 * A helper that generates a URL based on route name and parameters list.
 *
 * It adds to the generated URL a token to protect from CSRF attacks.
 *
 * Example of usage:
 * <code>
 *   {{csrfProtectedRoute "hello" to="world"}}
 * </code>
 * The code above generates URL for route named "hello" and pass parameter
 * "to" equals to "world" to URL generator. CSRF token will be included to
 * the parmeters list.
 */
class CsrfProtectedRouteHelper extends RouteHelper
{
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

        $parameters['csrf_token'] = get_csrf_token();

        return $this->getRouter()->generate($route_name, $parameters);
    }
}
