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
 * Generates an Operator's Code field.
 */
class OperatorCodeGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    public function doGenerate()
    {
        $form = HTML5\html('form');
        $form->setAttributes(array(
            'action' => '',
            'onsubmit' => sprintf(
                ("if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 "
                    . "&amp;&amp; window.event.preventDefault) window.event.preventDefault();"
                . "this.newWindow = window.open(%s + '&amp;operator_code=' "
                    . "+ document.getElementById('mibewOperatorCodeField').value, 'mibew', '%s');"
                . "this.newWindow.focus();"
                . "this.newWindow.opener=window;"
                . "return false;"),
                $this->getChatUrlForJs(),
                $this->getPopupOptions()
            ),
            'id' => 'mibewOperatorCodeForm',
        ));
        $form->addChild(HTML5\html(
            'input',
            array(
                'type' => 'text',
                'id' => 'mibewOperatorCodeField',
            )
        ));

        $button = HTML5\html('fragment');
        $button->addChild(HTML5\html('comment', 'mibew operator code field'));
        $button->addChild($form);
        $button->addChild(HTML5\html('comment', '/ mibew operator code field'));

        return $button;
    }
}
