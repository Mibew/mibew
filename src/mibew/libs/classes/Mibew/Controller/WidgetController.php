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

use Mibew\Asset\Generator\UrlGeneratorInterface as AssetUrlGeneratorInterface;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Settings;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Processes widget requests.
 */
class WidgetController extends AbstractController
{
    /**
     * Provides a gateway for widget requests.
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::VISITOR_TRACK} event.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $operator = array();
        $response_data = array(
            'load' => array(),
            'handlers' => array(),
            'dependencies' => array(),
            'data' => array(),
        );

        $tracking_allowed = (Settings::get('enabletracking') == '1')
            && (Settings::get('trackoperators') == '1' || !$this->getOperator());

        if ($tracking_allowed) {
            $entry = $request->query->get('entry', '');
            $referer = $request->server->get('HTTP_REFERER', '');
            $user_id = $request->query->get('user_id', false);

            // Check if session was started
            if (isset($_SESSION[SESSION_PREFIX . 'visitorid']) && preg_match('/^[0-9]+$/', $_SESSION[SESSION_PREFIX . 'visitorid'])) {
                // Session was started. Just track the visitor.
                $visitor_id = track_visitor($_SESSION[SESSION_PREFIX . 'visitorid'], $entry, $referer);
                $visitor = track_get_visitor_by_id($visitor_id);
            } else {
                $visitor = track_get_visitor_by_user_id($user_id);
                if ($visitor !== false) {
                    // Session is not started but the visitor exists in
                    // database. Probably third-party cookies are disabled by
                    // the browser. Use tracking by local cookie at target site.
                    $visitor_id = track_visitor($visitor['visitorid'], $entry, $referer);
                } else {
                    // Start tracking session
                    $visitor_id = track_visitor_start($entry, $referer);
                    $visitor = track_get_visitor_by_id($visitor_id);
                }
            }

            if ($visitor_id) {
                $_SESSION[SESSION_PREFIX . 'visitorid'] = $visitor_id;
            }

            if ($user_id === false) {
                // Update local cookie value at target site
                $response_data['handlers'][] = 'updateUserId';
                $response_data['dependencies']['updateUserId'] = array();
                $response_data['data']['user']['id'] = $visitor['userid'];
            }

            // Provide an ability for others to make something on visitor
            // tracking
            $event_arguments = array('visitor' => $visitor);
            EventDispatcher::getInstance()->triggerEvent(Events::VISITOR_TRACK, $event_arguments);

            // Get invitation state
            $invitation_state = invitation_state($visitor_id);

            // Check if invitation is closed
            if (!$invitation_state['invited'] && !empty($_SESSION[SESSION_PREFIX . 'invitation_threadid'])) {
                $response_data['handlers'][] = 'invitationClose';
                $response_data['dependencies']['invitationClose'] = array();
                unset($_SESSION[SESSION_PREFIX . 'invitation_threadid']);
            }

            // Check if the visitor is just invited to chat
            $is_invited = $invitation_state['invited']
                && (empty($_SESSION[SESSION_PREFIX . 'invitation_threadid'])
                    ? true
                    : ($_SESSION[SESSION_PREFIX . 'invitation_threadid'] != $invitation_state['threadid']));

            if ($is_invited) {
                // Load invitation thread
                $thread = Thread::load($invitation_state['threadid']);

                // Get operator info
                $operator = operator_by_id($thread->agentId);
                $locale = $request->query->get('locale', '');
                $operator_name = ($locale == get_home_locale())
                    ? $operator['vclocalename']
                    : $operator['vccommonname'];
                $avatar_url = $operator['vcavatar']
                    ? $this->asset($operator['vcavatar'], AssetUrlGeneratorInterface::ABSOLUTE_URL)
                    : false;

                // Show invitation dialog at widget side
                $response_data['handlers'][] = 'invitationCreate';
                $response_data['dependencies']['invitationCreate'] = array();
                $response_data['data']['invitation'] = array(
                    'operatorName' => htmlspecialchars($operator_name),
                    'avatarUrl' => htmlspecialchars($avatar_url),
                    'threadUrl' => $this->generateUrl(
                        'chat_user_invitation',
                        array(),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'acceptCaption' => getlocal('Answer'),
                );

                $_SESSION[SESSION_PREFIX . 'invitation_threadid'] = $thread->id;
            }

            // Check if the visitor rejects invitation
            if ($invitation_state['invited'] && $request->query->get('invitation_rejected')) {
                invitation_reject($visitor_id);
            }

            $event_arguments = array(
                'visitor' => $visitor,
                'request' => $request,
                'response' => $response_data,
                'route_url_generator' => $this->getRouter(),
                'asset_url_generator' => $this->getAssetManager()->getUrlGenerator(),
            );
            EventDispatcher::getInstance()->triggerEvent(Events::WIDGET_RESPONSE_ALTER, $event_arguments);
            $response_data = $event_arguments['response'];
        }

        // Builds JSONP response
        $response = new JsonResponse($response_data);
        $response->setCallback("Mibew.Objects.widget.onResponse");

        // Add headers to overcome third-party cookies problem.
        $response->headers->set('P3P', 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

        return $response;
    }
}
