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

namespace Mibew\Handlebars;

/**
 * Represents a set of default helpers for server side templates.
 */
class HelpersSet
{
    /**
     * Contains a list of helpers.
     *
     * @var array()
     */
    protected static $helpers = null;

    /**
     * Returns a set of handlebars helpers.
     *
     * @return array Helpers list that can be passed to
     * \Handlebars\Helpers::__construct();
     */
    public static function getHelpers()
    {
        if (!self::$helpers) {
            $blocks = new BlockStorage();
            self::$helpers = array(
                'l10n' => (new Helper\L10nHelper()),
                'extends' => (new Helper\ExtendsHelper()),
                'block' => (new Helper\BlockHelper($blocks)),
                'override' => (new Helper\OverrideHelper($blocks)),
                'ifOverridden' => (new Helper\IfOverriddenHelper($blocks)),
                'unlessOverridden' => (new Helper\UnlessOverriddenHelper($blocks)),
                'ifEqual' => (new Helper\IfEqualHelper()),
                'ifAny' => (new Helper\IfAnyHelper()),
                'ifEven' => (new Helper\IfEvenHelper()),
                'ifOdd' => (new Helper\IfOddHelper()),
                'generatePagination' => (new Helper\GeneratePaginationHelper()),
                'jsString' => (new Helper\JsStringHelper()),
                'repeat' => (new Helper\RepeatHelper()),
                'replace' => (new Helper\ReplaceHelper()),
                'formatDate' => (new Helper\FormatDateHelper()),
                'formatDateDiff' => (new Helper\FormatDateDiffHelper()),
                'cutString' => (new Helper\CutStringHelper()),
                'csrfTokenInput' => (new Helper\CsrfTokenInputHelper()),
                'csrfTokenInUrl' => (new Helper\CsrfTokenInUrlHelper()),
            );
        }

        return self::$helpers;
    }
}
