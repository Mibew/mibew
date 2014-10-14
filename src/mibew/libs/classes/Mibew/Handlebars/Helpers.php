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

use Handlebars\Helpers as BaseHelpers;

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

        $blocks = new BlockStorage();
        $this->add('l10n', new Helper\L10nHelper());
        $this->add('extends', new Helper\ExtendsHelper());
        $this->add('block', new Helper\BlockHelper($blocks));
        $this->add('override', new Helper\OverrideHelper($blocks));
        $this->add('ifOverridden', new Helper\IfOverriddenHelper($blocks));
        $this->add('unlessOverridden', new Helper\UnlessOverriddenHelper($blocks));
        $this->add('ifEqual', new Helper\IfEqualHelper());
        $this->add('ifAny', new Helper\IfAnyHelper());
        $this->add('ifEven', new Helper\IfEvenHelper());
        $this->add('ifOdd', new Helper\IfOddHelper());
        $this->add('generatePagination', new Helper\GeneratePaginationHelper());
        $this->add('repeat', new Helper\RepeatHelper());
        $this->add('replace', new Helper\ReplaceHelper());
        $this->add('formatDate', new Helper\FormatDateHelper());
        $this->add('formatDateDiff', new Helper\FormatDateDiffHelper());
        $this->add('cutString', new Helper\CutStringHelper());
        $this->add('csrfTokenInput', new Helper\CsrfTokenInputHelper());
    }
}
