<?php
/*
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

namespace Mibew\Handlebars;

/**
 * Set of helpers to for server side templates
 */
class HelpersSet
{
    /**
     * Contains an instance of helpers set.
     *
     * @var \Mibew\Handlebars\HelpersSet
     */
    protected static $instance = null;

    /**
     * Storage for overridable blocks' content.
     *
     * This storage is used by "block" helper and "override" helper.
     *
     * @var array
     */
    protected $blocksStorage = array();

    /**
     * Returns a set of handlebars helpers.
     *
     * @return array Helpers list that can be passed to
     * \Handlebars\Helpers::__construct();
     */
    public static function getHelpers()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance->helpersList();
    }

    /**
     * A helper for string localization.
     *
     * Example of usage:
     * <code>
     *   {{l10n "localization.string" arg1 arg2 arg3}}
     * </code>
     * where:
     *   - "localization.string" is localization constant.
     *   - arg* are arguments that will be passed to getlocal2 function. There
     *     can be arbitrary number of such arguments.
     */
    public function localizationHelper($template, $context, $args, $source)
    {
        // Check if there is at least one argument
        $parsed_arguments = $template->parseArguments($args);
        if (empty($parsed_arguments)) {
            return '';
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

        if (empty($local_args)) {
            $result = getlocal($text);
        } else {
            $result = getlocal2($text, $local_args);
        }

        return new \Handlebars\SafeString($result);
    }

    /**
     * Conditional helper that checks if two values are equal or not.
     *
     * Example of usage:
     * <code>
     *   {{#ifEqual first second}}
     *     The first argument is equal to the second one.
     *   {{else}}
     *     The arguments are not equal.
     *   {{/ifEqual}}
     * </code>
     */
    public function ifEqualHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args) || count($parsed_args) < 2) {
            return '';
        }

        $condition = ($context->get($parsed_args[0]) == $context->get($parsed_args[1]));

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

    /**
     * Conditional helper that checks if specified argument is even or not.
     *
     * Example of usage:
     * <code>
     *   {{#ifEven value}}
     *     The value is even.
     *   {{else}}
     *     The value is odd.
     *   {{/ifEven}}
     * </code>
     */
    public function ifEvenHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }

        $condition = ($context->get($parsed_args[0]) % 2 == 0);

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

    /**
     * Conditional helper that checks if specified argument is odd or not.
     *
     * Example of usage:
     * <code>
     *   {{#ifOdd value}}
     *     The value is odd.
     *   {{else}}
     *     The value is even.
     *   {{/ifOdd}}
     * </code>
     */
    public function ifOddHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }

        $condition = ($context->get($parsed_args[0]) % 2 == 1);

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
    public function ifAnyHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }

        $condition = false;
        foreach ($parsed_args as $parsed_arg) {
            $value = $context->get($parsed_arg);

            if ($value instanceof \Handlebars\String) {
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
    public function extendsHelper($template, $context, $args, $source)
    {
        // Get name of the parent template
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }
        $parent_template = $context->get(array_shift($parsed_args));

        // Render content inside "extends" block to override blocks
        $template->render($context);

        // We need to another instance of \Handlebars\Template to render parent
        // template. It can be got from Handlebars engine, so get the latter.
        $handlebars = $template->getEngine();

        // Render the parent template
        return $handlebars->render($parent_template, $context);
    }

    /**
     * A helper for defining default content of a block.
     *
     * Example of usage:
     * <code>
     *   {{#block "blockName"}}
     *     Default content for the block
     *   {{/block}}
     * </code>
     */
    public function blockHelper($template, $context, $args, $source)
    {
        // Get block name
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }
        $block_name = $context->get(array_shift($parsed_args));

        // If the block is not overridden render and show the default value
        if (!isset($this->blocksStorage[$block_name])) {
            return $template->render($context);
        }

        // Show overridden content
        return $this->blocksStorage[$block_name];
    }

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
    public function overrideHelper($template, $context, $args, $source)
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
        if (!isset($this->blocksStorage[$block_name])) {
            $this->blocksStorage[$block_name] = $template->render($context);
        }
    }

    /**
     * Conditional helper that checks if block overridden or not.
     *
     * Example of usage:
     * <code>
     *   {{#ifOverridden "blockName"}}
     *     The block was overridden
     *   {{else}}
     *     The block was not overridden
     *   {{/ifOverriden}}
     * </code>
     */
    public function ifOverriddenHelper($template, $context, $args, $source)
    {
        // Get block name
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }
        $block_name = $context->get(array_shift($parsed_args));

        // Check condition and render blocks
        if (isset($this->blocksStorage[$block_name])) {
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

    /**
     * Conditional helper that checks if block overridden or not.
     *
     * Example of usage:
     * <code>
     *   {{#unlessOverridden "blockName"}}
     *     The block was not overridden
     *   {{else}}
     *     The block was overridden
     *   {{/unlessOverriden}}
     * </code>
     */
    public function unlessOverriddenHelper($template, $context, $args, $source)
    {
        // Get block name
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }
        $block_name = $context->get(array_shift($parsed_args));

        // Check condition and render blocks
        if (!isset($this->blocksStorage[$block_name])) {
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

    /**
     * Generates markup with hidden input tag for CSRF token.
     *
     * Example of usage:
     * <code>
     *   {{csrfTokenInput}}
     * </code>
     */
    public function csrfTokenInputHelper()
    {
        return new \Handlebars\SafeString(get_csrf_token_input());
    }

    /**
     * Generates CSRF taken formated prepared to insert in URLs.
     *
     * Example of usage:
     * <code>
     *   {{csrfTokenInUrl}}
     * </code>
     */
    public function csrfTokenInUrlHelper()
    {
        return new \Handlebars\SafeString(get_csrf_token_in_url());
    }

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
    public function generatePaginationHelper($template, $context, $args, $source)
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

        return new \Handlebars\SafeString($pagination);
    }

    /**
     * Escapes special characters to use result as a valid JavaScript string
     * enclosed with single quotes (') or duouble quotes (").
     *
     * Example of usage:
     * <code>
     *   var a = "{{#jsString}}some string to escape{{/jsString}}";
     * </code>
     */
    public function jsStringHelper($template, $context, $args, $source)
    {
        return str_replace("\n", "\\n", addslashes($template->render($context)));
    }

    /**
     * Helper for repeating content.
     *
     * Example of usage:
     * <code>
     *   {{#repeat times}}content to repeat{{/repeat}}
     * </code>
     */
    public function repeatHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }

        $times = intval($context->get($parsed_args[0]));
        $string = $template->render($context);

        return str_repeat($string, $times);
    }

    /**
     * Helper for replacing substrings.
     *
     * Example of usage:
     * <code>
     *   {{#replace search replacement}}target content{{/replace}}
     * </code>
     */
    public function replaceHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args) || count($parsed_args) < 2) {
            return '';
        }

        $search = $context->get($parsed_args[0]);
        $replacement = $context->get($parsed_args[1]);
        $subject = $template->render($context);

        return str_replace($search, $replacement, $subject);
    }

    /**
     * Format date using internal format.
     *
     * Example of usage:
     * <code>
     *   {{formatDate unixTimestamp}}
     * </code>
     */
    public function formatDateHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }

        $timestamp = intval($context->get($parsed_args[0]));

        return date_to_text($timestamp);
    }

    /**
     * Format date difference using internal format.
     *
     * Example of usage:
     * <code>
     *   {{formatDateDiff seconds}}
     * </code>
     */
    public function formatDateDiffHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args)) {
            return '';
        }

        $seconds = intval($context->get($parsed_args[0]));

        return date_diff_to_text($seconds);
    }

    /**
     * Cut string if it exceeds specified length.
     *
     * Example of usage:
     * <code>
     *   {{cutString string length}}
     * </code>
     */
    public function cutStringHelper($template, $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (empty($parsed_args) || count($parsed_args) < 2) {
            return '';
        }

        $string = $context->get($parsed_args[0]);
        $length = intval($context->get($parsed_args[1]));

        return substr($string, 0, $length);
    }

    /**
     * Actually builds helpers list.
     *
     * @return array List of helpers
     */
    protected function helpersList()
    {
        return array(
            'l10n' => array($this, 'localizationHelper'),
            'extends' => array($this, 'extendsHelper'),
            'block' => array($this, 'blockHelper'),
            'override' => array($this, 'overrideHelper'),
            'ifOverridden' => array($this, 'ifOverriddenHelper'),
            'unlessOverridden' => array($this, 'unlessOverriddenHelper'),
            'ifEqual' => array($this, 'ifEqualHelper'),
            'ifAny' => array($this, 'ifAnyHelper'),
            'ifEven' => array($this, 'ifEvenHelper'),
            'ifOdd' => array($this, 'ifOddHelper'),
            'generatePagination' => array($this, 'generatePaginationHelper'),
            'jsString' => array($this, 'jsStringHelper'),
            'repeat' => array($this, 'repeatHelper'),
            'replace' => array($this, 'replaceHelper'),
            'formatDate' => array($this, 'formatDateHelper'),
            'formatDateDiff' => array($this, 'formatDateDiffHelper'),
            'cutString' => array($this, 'cutStringHelper'),
            'csrfTokenInput' => array($this, 'csrfTokenInputHelper'),
            'csrfTokenInUrl' => array($this, 'csrfTokenInUrlHelper'),
        );
    }
}
