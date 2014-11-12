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

namespace Mibew\Button\Generator;

use Canteen\HTML5;

/**
 * Generates a Text button.
 */
class TextGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $button = HTML5\html('fragment');
        $button->addChild(HTML5\html('comment', 'mibew text link'));
        $button->addChild($this->getPopup($this->getOption('caption')));
        $button->addChild(HTML5\html('comment', '/ mibew text link'));

        return (string)$button;
    }

    /**
     * Generates a markup for opening popup window with the chat.
     *
     * @return string HTML markup.
     */
    protected function getPopup($message)
    {
        $link = HTML5\html('a', $message);

        $link->setAttributes(array(
            'id' => 'mibewAgentButton',
            'href' =>  str_replace('&', '&amp;', $this->getChatUrl()),
            'target' => '_blank',
            'onclick' =>sprintf(
                ("if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 "
                    . "&amp;&amp; window.event.preventDefault) window.event.preventDefault();"
                    . "this.newWindow = window.open(%s, 'mibew', '%s');"
                    . "this.newWindow.focus();"
                    . "this.newWindow.opener=window;"
                    . "return false;"),
                $this->getChatUrlForJs(),
                $this->getPopupOptions()
            ),
        ));

        $title = $this->getOption('title');
        if ($title) {
            $link->setAttribute('title', $title);
        }

        return $link;
    }
}
