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
 * Generates an Operator's Code field.
 */
class OperatorCodeGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $js_link = $this->getChatUrlForJs();
        $popup_options = $this->getPopupOptions();

        $form_on_submit = "if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 "
            . "&amp;&amp; window.event.preventDefault) window.event.preventDefault();"
            . "this.newWindow = window.open({$js_link} + '&amp;operator_code=' "
            . "+ document.getElementById('mibewOperatorCodeField').value, 'mibew', '{$popup_options}');"
            . "this.newWindow.focus();this.newWindow.opener=window;return false;";

        $temp = '<form action="" onsubmit="' . $form_on_submit . '" id="mibewOperatorCodeForm">'
            . '<input type="text" id="mibewOperatorCodeField" />'
            . '</form>';

        return "<!-- mibew operator code field -->" . $temp . "<!-- / mibew operator code field -->";
    }
}
