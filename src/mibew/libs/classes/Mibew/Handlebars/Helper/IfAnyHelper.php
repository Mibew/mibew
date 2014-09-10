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

namespace Mibew\Handlebars\Helper;

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\String;
use Handlebars\Template;

/**
 * Conditional helper that checks if at least one argumet can be treated as
 * "true" value.
 *
 * Example of usage:
 * <code>
 *   {{#ifAny first second third}}
 *     At least one of argument can be threated as "true".
 *   {{else}}
 *     All values are "falsy"
 *   {{/ifAny}}
 * </code>
 */
class IfAnyHelper implements HelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (count($parsed_args) == 0) {
            throw new \InvalidArgumentException(
                '"ifAny" helper expects at least one argument.'
            );
        }

        $condition = false;
        foreach ($parsed_args as $parsed_arg) {
            $value = $context->get($parsed_arg);

            if ($value instanceof String) {
                // We need to get internal string. Casting any object of
                // \Handlebars\String will have positive result even for those
                // with empty internal strings.
                $value = $value->getString();
            }

            if ($value) {
                $condition = true;
                break;
            }
        }

        if ($condition) {
            $template->setStopToken('else');
            $buffer = $template->render($context);
            $template->setStopToken(false);
        } else {
            $template->setStopToken('else');
            $template->discard();
            $template->setStopToken(false);
            $buffer = $template->render($context);
        }

        return $buffer;
    }
}
