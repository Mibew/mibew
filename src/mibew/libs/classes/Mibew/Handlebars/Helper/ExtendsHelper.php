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
use Handlebars\Template;

/**
 * A helper for templates inheritance.
 *
 * Example of usage:
 * <code>
 *   {{#extends "parentTemplateName"}}
 *     {{#override "blockName"}}
 *       Overridden first block
 *     {{/override}}
 *
 *     {{#override "anotherBlockName"}}
 *       Overridden second block
 *     {{/override}}
 *   {{/extends}}
 * </code>
 */
class ExtendsHelper implements HelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        // Get name of the parent template
        $parsed_args = $template->parseArguments($args);
        if (count($parsed_args) != 1) {
            throw new \InvalidArgumentException(
                '"extends" helper expects exactly one argument.'
            );
        }
        $parent_template = $context->get(array_shift($parsed_args));

        // Render content inside "extends" block to override blocks
        $template->render($context);

        // We need another instance of \Handlebars\Template to render parent
        // template. It can be got from Handlebars engine, so get the engine.
        $handlebars = $template->getEngine();

        // Render the parent template
        return $handlebars->render($parent_template, $context);
    }
}
