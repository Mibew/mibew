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

use Mibew\Asset\AssetManagerInterface;
use Mibew\Http\Exception\NotFoundException;
use Mibew\Settings;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with user's chat window.
 */
class UserChatController extends AbstractController
{
    /**
     * Process chat pages.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content or a redirect response.
     * @throws NotFoundException If the thread with specified ID and token is
     * not found.
     */
    public function indexAction(Request $request)
    {
        $thread_id = $request->attributes->getInt('thread_id');
        $token = $request->attributes->get('token');

        // We have to check that the thread is owned by the user.
        $is_own_thread = isset($_SESSION[SESSION_PREFIX . 'own_threads'])
            && in_array($thread_id, $_SESSION[SESSION_PREFIX . 'own_threads']);

        $thread = Thread::load($thread_id, $token);
        if (!$thread || !$is_own_thread) {
            throw new NotFoundException('The thread is not found.');
        }

        $page = setup_chatview_for_user(
            $this->getRouter(),
            $this->getAssetManager()->getUrlGenerator(),
            $request,
            $thread
        );

        // Build js application options
        $page['chatOptions'] = $page['chat'];

        // Initialize client side application
        $this->getAssetManager()->attachJs('js/compiled/chat_app.js');
        $this->getAssetManager()->attachJs(
            $this->startJsApplication($request, $page),
            AssetManagerInterface::INLINE,
            1000
        );

        // Expand page
        return $this->render('chat', $page);
    }

    /**
     * Check chat to exists.
     *
     * @param Request $request Incoming request.
     * @return string Empty string.
     * @throws NotFoundException If the thread with specified ID and token is
     * not found.
     */
    public function checkAction(Request $request)
    {
        $thread_id = $request->attributes->getInt('thread_id');
        $token = $request->attributes->get('token');

        // We have to check that the thread is owned by the user.
        $is_own_thread = isset($_SESSION[SESSION_PREFIX . 'own_threads'])
            && in_array($thread_id, $_SESSION[SESSION_PREFIX . 'own_threads']);

        $thread = Thread::load($thread_id, $token);
        if (!$thread || !$is_own_thread) {
            throw new NotFoundException('The thread is not found.');
        }

        return "";
    }

    /**
     * Starts the chat.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     *   Rendered page content or a redirect response.
     * @todo Split the action into pieces.
     */
    public function startAction(Request $request)
    {
        // Check if we should force the user to use SSL
        $ssl_redirect = $this->sslRedirect($request);
        if ($ssl_redirect !== false) {
            return $ssl_redirect;
        }

        // Initialize client side application
        $this->getAssetManager()->attachJs('js/compiled/chat_app.js');

        $thread = null;
        // Try to get thread from the session
        if (isset($_SESSION[SESSION_PREFIX . 'threadid'])) {
            $thread = Thread::reopen($_SESSION[SESSION_PREFIX . 'threadid']);
        }

        // Create new thread
        if (!$thread) {
            // Load group info
            $group_id = 0;
            $group_name = '';
            $group = null;
            if (Settings::get('enablegroups') == '1') {
                // ID of the group can be either positive integer or zero.
                $group_id = max(0, $request->query->getInt('group'));

                if ($group_id) {
                    $group = group_by_id($group_id);
                    if (!$group) {
                        $group_id = 0;
                    } else {
                        $group_name = get_group_name($group);
                    }
                }
            }

            // Get operator code
            $operator_code = $request->query->get('operator_code');
            if (!preg_match("/^[A-z0-9_]+$/", $operator_code)) {
                $operator_code = false;
            }

            // Get visitor info
            $visitor = visitor_from_request();
            $info = $request->query->get('info');
            $email = $request->query->get('email');

            // Get referrer
            $referrer = $request->query->get('url', $request->headers->get('referer'));
            if ($request->query->get('referrer')) {
                $referrer .= "\n" . $request->query->get('referrer');
            }

            // Check if there are online operators
            if (!has_online_operators($group_id)) {
                // Display leave message page
                $page = array_merge_recursive(
                    setup_logo($group),
                    setup_leavemessage(
                        $visitor['name'],
                        $email,
                        $group_id,
                        $info,
                        $referrer
                    )
                );
                $page['leaveMessageOptions'] = $page['leaveMessage'];

                $this->getAssetManager()->attachJs(
                    $this->startJsApplication($request, $page),
                    AssetManagerInterface::INLINE,
                    1000
                );

                return $this->render('chat', $page);
            }

            // Get invitation info
            if (Settings::get('enabletracking') && !empty($_SESSION[SESSION_PREFIX . 'visitorid'])) {
                $invitation_state = invitation_state($_SESSION[SESSION_PREFIX . 'visitorid']);
                $visitor_is_invited = $invitation_state['invited'];
            } else {
                $visitor_is_invited = false;
            }

            // Get operator info
            $requested_operator = false;
            if ($operator_code) {
                $requested_operator = operator_by_code($operator_code);
            }

            // Check if survey should be displayed
            if (Settings::get('enablepresurvey') == '1' && !$visitor_is_invited && !$requested_operator) {
                // Display prechat survey
                $page = array_merge_recursive(
                    setup_logo($group),
                    setup_survey(
                        $visitor['name'],
                        $email,
                        $group_id,
                        $info,
                        $referrer
                    )
                );
                $page['surveyOptions'] = $page['survey'];

                $this->getAssetManager()->attachJs(
                    $this->startJsApplication($request, $page),
                    AssetManagerInterface::INLINE,
                    1000
                );

                return $this->render('chat', $page);
            }

            // Start chat thread
            $thread = chat_start_for_user(
                $group_id,
                $requested_operator,
                $visitor['id'],
                $visitor['name'],
                $referrer,
                $info
            );
        }
        $path_args = array(
            'thread_id' => intval($thread->id),
            'token' => urlencode($thread->lastToken),
        );

        $chat_style_name = $request->query->get('style');
        if (preg_match("/^\w+$/", $chat_style_name)) {
            $path_args['style'] = $chat_style_name;
        }

        return $this->redirect($this->generateUrl('chat_user', $path_args));
    }

    /**
     * Process chat in an invitation block.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse Rendered
     *   page content or a redirect response.
     */
    public function invitationAction(Request $request)
    {
        // Check if an user tries to use invitation functionality when it's
        // disabled.
        if (!Settings::get('enabletracking')) {
            return $this->redirect($this->generateUrl('chat_user_start'));
        }

        // Check if we should force the user to use SSL.
        $ssl_redirect = $this->sslRedirect($request);
        if ($ssl_redirect !== false) {
            return $ssl_redirect;
        }

        // Check if user invited to chat.
        $invitation_state = invitation_state($_SESSION[SESSION_PREFIX . 'visitorid']);

        if (!$invitation_state['invited'] || !$invitation_state['threadid']) {
            return $this->redirect($this->generateUrl('chat_user_start'));
        }

        $thread = Thread::load($invitation_state['threadid']);

        // Store own thread ids to restrict access for other people
        if (!isset($_SESSION[SESSION_PREFIX . 'own_threads'])) {
            $_SESSION[SESSION_PREFIX . 'own_threads'] = array();
        }
        $_SESSION[SESSION_PREFIX . 'own_threads'][] = $thread->id;

        // Prepare page
        $page = setup_invitation_view($thread);

        // Build js application options
        $page['invitationOptions'] = $page['invitation'];

        // Initialize client side application
        $this->getAssetManager()->attachJs('js/compiled/chat_app.js');
        $this->getAssetManager()->attachJs(
            $this->startJsApplication($request, $page),
            AssetManagerInterface::INLINE,
            1000
        );

        // Expand page
        return $this->render('chat', $page);
    }
}
