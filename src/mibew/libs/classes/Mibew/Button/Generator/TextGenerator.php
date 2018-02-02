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

namespace Mibew\Button\Generator;

use Canteen\HTML5;

/**
 * Generates a Text button.
 */
class TextGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    public function doGenerate()
    {
        $button = HTML5\html('fragment');
        $button->addChild(HTML5\html('comment', 'mibew text link'));
        $button->addChild($this->getPopupLink($this->getOption('caption')));
        $button->addChild(HTML5\html('comment', '/ mibew text link'));

        return $button;
    }

    /**
     * Generates a markup for link that opens chat popup.
     *
     * @param string|\Canteen\HTML5\Node $caption A string or an HTML node that
     * is used as popup link caption.
     * @return string HTML markup.
     */
    protected function getPopupLink($caption)
    {
        $link = HTML5\html('a', $caption);
        $link->setAttributes(array(
            'id' => 'mibew-agent-button',
            'href' =>  str_replace('&', '&amp;', $this->getChatUrl()),
            'target' => '_blank',
            'onclick' => ("Mibew.Objects.ChatPopups['" . $this->getOption('unique_id') . "'].open();"
                . "return false;"),
        ));

        $title = $this->getOption('title');
        if ($title) {
            $link->setAttribute('title', $title);
        }

        $fragment = HTML5\html('fragment');
        $fragment->addChild($link);
        $fragment->addChild($this->getPopup());

        return $fragment;
    }
}
