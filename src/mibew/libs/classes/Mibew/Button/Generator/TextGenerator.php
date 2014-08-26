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
        return "<!-- mibew button -->"
            . $this->getPopup($this->getOption('caption'))
            . "<!-- / mibew button -->";
    }

    /**
     * Generates a markup for opening popup window with the chat.
     *
     * @return string HTML markup.
     */
    protected function getPopup($message)
    {
        $url = str_replace('&', '&amp;', $this->getChatUrl());
        $js_url = $this->getChatUrlForJs();
        $options = $this->getPopupOptions();
        $title = $this->getOption('title');

        return "<a id=\"mibewAgentButton\" href=\"$url\" target=\"_blank\" "
            . ($title ? "title=\"$title\" " : "")
            . "onclick=\"if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 "
            . "&amp;&amp; window.event.preventDefault) window.event.preventDefault();"
            . "this.newWindow = window.open($js_url, 'mibew', '$options');"
            . "this.newWindow.focus();this.newWindow.opener=window;return false;\">$message</a>";
    }
}
