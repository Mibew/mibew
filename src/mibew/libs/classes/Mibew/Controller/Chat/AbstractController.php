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

namespace Mibew\Controller\Chat;

use Mibew\Controller\AbstractController as BaseAbstractController;
use Mibew\Settings;
use Mibew\Style\ChatStyle;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains base actions which are related with operator's and user's chat
 * windows.
 */
abstract class AbstractController extends BaseAbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function getStyle()
    {
        if (is_null($this->style)) {
            $this->style = $this->prepareStyle(new ChatStyle(ChatStyle::getCurrentStyle()));
        }

        return $this->style;
    }

    /**
     * Checks if the user should be forced to use SSL connections.
     *
     * @param Request $request Request to check.
     * @return boolean|\Symfony\Component\HttpFoundation\RedirectResponse False
     *   if the redirect is not needed and redirect response object otherwise.
     */
    protected function sslRedirect(Request $request)
    {
        $need_redirect = Settings::get('enablessl') == '1'
            && Settings::get('forcessl') == '1'
            && !$request->isSecure();

        if (!$need_redirect) {
            return false;
        }

        if (null !== ($qs = $request->getQueryString())) {
            $qs = '?'.$qs;
        }

        $path = 'https://' . $request->getHttpHost() . $request->getBasePath()
            . $request->getPathInfo() . $qs;

        return $this->redirect($path);
    }

    /**
     * Generates JavaScript code that starts client side application.
     *
     * @param Request $request Incoming request.
     * @param array $options Client side application options. At the moment the
     *   method accepts the following options:
     *   - "company": array, a set of company info. See {@link setup_logo()}
     *     for details.
     *   - "mibewHost": string, a URL which is used as a Mibew Messenger host.
     *     See {@link setup_logo()} for details.
     *   - "page.title": string, a value which will be used as a page title.
     *   - "startFrom": string, indicates what module should be invoked first.
     *   - "chatOptions": array, (optional) list of chat module options.
     *   - "surveyOptions": array, (optional) list of pre-chat survey module
     *     options.
     *   - "leaveMessageOptions": array, (optional) list of leave message module
     *     options.
     *   - "invitationOptions": array, (optional) list of invitation module
     *     options.
     * @return string JavaScript code that starts "users" client side
     *   application.
     * @todo The way options passed here should be reviewed. The method must get
     *   finite number of well-structured arguments.
     */
    protected function startJsApplication(Request $request, $options)
    {
        $app_settings = array(
            'server' => array(
                'url' => $this->generateUrl('chat_thread_update'),
                'requestsFrequency' => Settings::get('updatefrequency_chat'),
            ),
            'page' => array(
                'style' => $this->getStyle()->getName(),
                'mibewBasePath' => $request->getBasePath(),
                'mibewBaseUrl' => $request->getBaseUrl(),
                'stylePath' => $request->getBasePath() . '/' . $this->getStyle()->getFilesPath(),
                'company' => isset($options['company']) ? $options['company'] : '',
                'mibewHost' => isset($options['mibewHost']) ? $options['mibewHost'] : '',
                'title' => isset($options['page.title']) ? $options['page.title'] : '',
            ),
            'startFrom' => $options['startFrom'],
        );

        // Add module specific options
        $module_options_list = array(
            'chatOptions',
            'surveyOptions',
            'leaveMessageOptions',
            'invitationOptions',
        );
        foreach ($module_options_list as $key) {
            if (isset($options[$key])) {
                $app_settings[$key] = $options[$key];
            }
        }

        return sprintf(
            'jQuery(document).ready(function() {Mibew.Application.start(%s);});',
            json_encode($app_settings)
        );
    }
}
