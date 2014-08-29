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
use Handlebars\SafeString;
use Handlebars\Template;

/**
 * Generates pagination block.
 *
 * Example of usage:
 * <code>
 *   {{generatePagination stylePath paginationInfo bottom}}
 * </code>
 * where:
 *   - "stylePath" is expression for path to current style.
 *   - "paginationInfo" is 'info' key from the result of setup_pagination
 *     function.
 *   - "bottom": optional argument that indicate if pagination block shoud
 *     be generated for a page bottom or not. If specified and equal to
 *     string "false" then boolean false will be passed into
 *     generate_pagination. In all other cases boolean true will be used.
 */
class GeneratePaginationHelper implements HelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args) || count($parsed_args) < 2) {
            return '';
        }

        $stylePath = $context->get($parsed_args[0]);
        $pagination_info = $context->get($parsed_args[1]);
        $bottom = empty($parsed_args[2]) ? true : $context->get($parsed_args[2]);

        $pagination = generate_pagination(
            $stylePath,
            $pagination_info,
            ($bottom === "false") ? false : true
        );

        return new SafeString($pagination);
    }
}
