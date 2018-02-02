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

namespace Mibew\Controller;

use Mibew\Settings;
use Mibew\Style\ChatStyle;
use Mibew\RequestProcessor\UsersProcessor;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions for all awaiting users-related functionality.
 */
class UsersController extends AbstractController
{
    /**
     * Generates a page with awaiting visitors.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $operator = $this->getOperator();

        // Operator becomes online as soon as he open "operator/users" page
        notify_operator_alive($operator['operatorid'], 0);
        $operator['istatus'] = 0;
        $this->getAuthenticationManager()->setOperator($operator);

        $_SESSION[SESSION_PREFIX . "operatorgroups"] = get_operator_groups_list($operator['operatorid']);

        $page = array();
        $page['showonline'] = (Settings::get('showonlineoperators') == '1');
        $page['showvisitors'] = (Settings::get('enabletracking') == '1');
        $page['title'] = getlocal("List of visitors waiting");
        $page['menuid'] = "users";

        $page = array_merge($page, prepare_menu($operator));

        $page['hideMenu'] = (bool)$request->query->has('nomenu');

        // Attach files of the client side application and start it
        $this->getAssetManager()->attachJs('js/compiled/users_app.js');
        $this->getAssetManager()->attachJs(
            $this->startJsApplication($request, $operator),
            \Mibew\Asset\AssetManagerInterface::INLINE,
            1000
        );

        return $this->render('users', $page);
    }

    /**
     * Provides a gateway for client side application at awaiting visitors page.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function updateAction(Request $request)
    {
        $processor = UsersProcessor::getInstance();
        $processor->setAuthenticationManager($this->getAuthenticationManager());

        return $processor->handleRequest($request);
    }

    /**
     * Generates JavaScript code that starts client side application.
     *
     * @param Request $request Incoming request.
     * @param array $operator Current operator.
     * @return string JavaScript code that starts "users" client side
     *   application.
     */
    protected function startJsApplication(Request $request, $operator)
    {
        // Load dialogs style options
        $chat_style = new ChatStyle(ChatStyle::getCurrentStyle());
        $chat_style_config = $style_config = $chat_style->getConfigurations();

        // Load page style options
        $page_style_config = $style_config = $this->getStyle()->getConfigurations();

        return sprintf(
            'jQuery(document).ready(function() {Mibew.Application.start(%s);});',
            json_encode(array(
                'server' => array(
                    'url' => $this->generateUrl('users_update'),
                    'requestsFrequency' => Settings::get('updatefrequency_operator'),
                ),
                'agent' => array(
                    'id' => $operator['operatorid'],
                ),
                'page' => array(
                    'mibewBasePath' => $request->getBasePath(),
                    'mibewBaseUrl' => $request->getBaseUrl(),

                    'showOnlineOperators' => (Settings::get('showonlineoperators') == '1'),
                    'showVisitors' => (Settings::get('enabletracking') == '1'),
                    'showPopup' => (Settings::get('enablepopupnotification') == '1'),

                    'threadTag' => $page_style_config['users']['thread_tag'],
                    'visitorTag' => $page_style_config['users']['visitor_tag'],

                    'agentLink' => $request->getBaseUrl() . '/operator/chat',
                    'trackedLink' => $request->getBaseUrl() . '/operator/history/user-track',
                    'banLink' => $request->getBaseUrl() . '/operator/ban',
                    'inviteLink' => $request->getBaseUrl() . '/operator/invite',

                    'chatWindowParams' => $chat_style_config['chat']['window'],
                    'trackedUserWindowParams' => $page_style_config['tracked']['user_window'],
                    'trackedVisitorWindowParams' => $page_style_config['tracked']['visitor_window'],
                    'banWindowParams' => $page_style_config['ban']['window'],
                    'inviteWindowParams' => $chat_style_config['chat']['window'],
                ),
            ))
        );
    }
}
