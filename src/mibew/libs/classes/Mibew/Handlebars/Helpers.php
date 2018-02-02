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

namespace Mibew\Handlebars;

use Handlebars\Helpers as BaseHelpers;
use JustBlackBird\HandlebarsHelpers;

/**
 * Handlebars helpers collection.
 *
 * This class differs from \Handlebars\Helpers in default helpers set.
 */
class Helpers extends BaseHelpers
{
    /**
     * {@inheritdoc}
     */
    protected function addDefaultHelpers()
    {
        parent::addDefaultHelpers();

        $this->add('l10n', new Helper\L10nHelper());
        $this->add('generatePagination', new Helper\GeneratePaginationHelper());
        $this->add('formatDate', new Helper\FormatDateHelper());
        $this->add('formatDateDiff', new Helper\FormatDateDiffHelper());
        $this->add('csrfTokenInput', new Helper\CsrfTokenInputHelper());

        // Use third party helpers
        $this->add('ifEqual', new HandlebarsHelpers\Comparison\IfEqualHelper());
        $this->add('ifAny', new HandlebarsHelpers\Comparison\IfAnyHelper());
        $this->add('ifEven', new HandlebarsHelpers\Comparison\IfEvenHelper());
        $this->add('ifOdd', new HandlebarsHelpers\Comparison\IfOddHelper());
        $this->add('repeat', new HandlebarsHelpers\Text\RepeatHelper());
        $this->add('replace', new HandlebarsHelpers\Text\ReplaceHelper());
        $this->add('cutString', new HandlebarsHelpers\Text\TruncateHelper());
        $blocks = new HandlebarsHelpers\Layout\BlockStorage();
        $this->add('extends', new HandlebarsHelpers\Layout\ExtendsHelper($blocks));
        $this->add('block', new HandlebarsHelpers\Layout\BlockHelper($blocks));
        $this->add('override', new HandlebarsHelpers\Layout\OverrideHelper($blocks));
        $this->add('ifOverridden', new HandlebarsHelpers\Layout\IfOverriddenHelper($blocks));
        $this->add('unlessOverridden', new HandlebarsHelpers\Layout\UnlessOverriddenHelper($blocks));
    }
}
