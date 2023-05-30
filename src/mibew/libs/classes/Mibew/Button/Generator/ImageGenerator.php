<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2023 the original author or authors.
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
use Mibew\Settings;

/**
 * Generates an Image button.
 */
class ImageGenerator extends TextGenerator
{
    /**
     * {@inheritdoc}
     */
    public function doGenerate()
    {
        $image_link_args = array(
            'i' => $this->getOption('image'),
            'lang' => $this->getOption('locale'),
        );

        if ($this->getOption('group_id')) {
            $image_link_args['group'] = $this->getOption('group_id');
        }

        $image_url = str_replace(
            '&',
            '&amp;',
            $this->generateUrl('button', $image_link_args)
        );
        $image = HTML5\html('img');
        $image->setAttributes(array(
            'src' => $image_url,
            'border' => 0,
            'alt' => '',
        ));

        $button = HTML5\html('fragment');
        $button->addChild(HTML5\html('comment', 'mibew button'));
        $button->addChild($this->getPopupLink($image));
        $button->addChild(HTML5\html('comment', '/ mibew button'));

        return $button;
    }
}
