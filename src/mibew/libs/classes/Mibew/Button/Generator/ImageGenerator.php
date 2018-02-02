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
use Mibew\Settings;
use Mibew\Style\InvitationStyle;

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
        if (Settings::get('enabletracking')) {
            $button->addChild($this->getWidgetCode());
        }
        $button->addChild(HTML5\html('comment', '/ mibew button'));

        return $button;
    }

    /**
     * Generates HTML markup for Mibew Messenger Widget.
     *
     * @return \Canteen\HTML5\Fragment
     */
    protected function getWidgetCode()
    {
        $widget_data = array();

        // Get actual invitation style instance
        $style_name = $this->getOption('invitation_style')
            ? $this->getOption('invitation_style')
            : InvitationStyle::getCurrentStyle();
        $style = new InvitationStyle($style_name);

        // URL of file with additional CSS rules for invitation popup
        $widget_data['inviteStyle'] = $this->generateAssetUrl(
            $style->getFilesPath() . '/invite.css'
        );

        // Time between requests to the server in milliseconds
        $widget_data['requestTimeout'] = Settings::get('updatefrequency_tracking') * 1000;

        // URL for requests
        $widget_data['requestURL'] = $this->generateUrl('widget_gateway');

        // Locale for invitation
        $widget_data['locale'] = $this->getOption('locale');

        // Name of the cookie to track user. It is used if third-party cookie
        // blocked
        $widget_data['visitorCookieName'] = VISITOR_COOKIE_NAME;

        $markup = HTML5\html('fragment');
        $markup->addChild(HTML5\html('div#mibew-invitation'));
        $markup->addChild(
            HTML5\html('script')->setAttributes(array(
                'type' => 'text/javascript',
                'src' => $this->generateAssetUrl('js/compiled/widget.js'),
            ))
        );
        $markup->addChild(
            HTML5\html('script')
                ->setAttribute('type', 'text/javascript')
                ->addChild('Mibew.Widget.init(' . json_encode($widget_data) . ')')
        );

        return $markup;
    }
}
