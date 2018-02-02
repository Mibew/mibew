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
use Handlebars\SafeString;
use Handlebars\Template;

/**
 * A helper for string localization.
 *
 * Example of usage:
 * <code>
 *   {{l10n "localization.string" arg1 arg2 arg3}}
 * </code>
 * where:
 *   - "localization.string" is localization constant.
 *   - arg* are arguments that will be passed to getlocal function. There
 *     can be arbitrary number of such arguments.
 */
class L10nHelper implements HelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        // Check if there is at least one argument
        $parsed_arguments = $template->parseArguments($args);
        if (count($parsed_arguments) == 0) {
            throw new \InvalidArgumentException(
                '"l10n" helper expects at least one argument.'
            );
        }

        $text = $context->get(array_shift($parsed_arguments));

        // We need to escape extra arguments passed to the helper. Thus we need
        // to get escape function and its arguments from the template engine.
        $escape_func = $template->getEngine()->getEscape();
        $escape_args = $template->getEngine()->getEscapeArgs();

        // Check if there are any other arguments passed into helper and escape
        // them.
        $local_args = array();
        foreach ($parsed_arguments as $parsed_argument) {
            // Get locale argument string and add it to escape function
            // arguments.
            array_unshift($escape_args, $context->get($parsed_argument));

            // Escape locale argument's value
            $local_args[] = call_user_func_array(
                $escape_func,
                array_values($escape_args)
            );

            // Remove locale argument's value from escape function argument
            array_shift($escape_args);
        }

        $result = getlocal($text, $local_args);

        return new SafeString($result);
    }
}
