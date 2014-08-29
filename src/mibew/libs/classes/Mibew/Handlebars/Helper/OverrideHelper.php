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
 * A helper for overriding content of a block.
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
class OverrideHelper extends AbstractBlockHelper implements HelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        // Get block name
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }
        $block_name = $context->get(array_shift($parsed_args));

        // We need to provide unlimited inheritence level. Rendering is started
        // from the deepest level template. If the content is in the block
        // storage it is related with the deepest level template. Thus we do not
        // need to override it.
        if (!$this->blocksStorage->has($block_name)) {
            $this->blocksStorage->set($block_name, $template->render($context));
        }
    }
}
