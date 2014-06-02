<?php
/*
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

namespace Mibew\Controller\Chat\User;

use Mibew\Controller\Chat\AbstractController;
use Mibew\Http\Exception\BadRequestException;
use Mibew\Settings;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with user's chat window.
 */
class ChatController extends AbstractController
{
    /**
     * Process chat pages.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse Rendered
     *   page content or a redirect response.
     * @throws BadRequestException If the thread cannot be loaded by some
     * reasons.
     */
    public function indexAction(Request $request)
    {
        // Check if we should force the user to use SSL
        $ssl_redirect = $this->sslRedirect($request);
        if ($ssl_redirect !== false) {
            return $ssl_redirect;
        }

        // Do not support old browsers at all
        if (get_remote_level($request->headers->get('User-Agent')) == 'old') {
            // Create page array
            $page = array_merge_recursive(
                setup_logo()
            );

            return $this->render('nochat', $page);
        }

        $action = $request->query->get('act');
        if (!in_array($action, array('invitation', 'mailthread'))) {
            $action = 'default';
        }

        if ($action == 'invitation' && Settings::get('enabletracking')) {
            // Check if user invited to chat
            $invitation_state = invitation_state($_SESSION['visitorid']);

            if ($invitation_state['invited'] && $invitation_state['threadid']) {
                $thread = Thread::load($invitation_state['threadid']);

                // Prepare page
                $page = setup_invitation_view($thread);

                // Build js application options
                $page['invitationOptions'] = json_encode($page['invitation']);

                // Expand page
                return $this->render('chat', $page);
            }
        }

        if (!$request->query->has('token') || !$request->query->has('thread')) {
            return $this->startChat($request);
        }

        // Get and validate thread id
        $thread_id = $request->query->get('thread');
        if (!preg_match("/^\d{1,10}$/", $thread_id)) {
            throw new BadRequestException('Wrong value of "thread" argument.');
        }

        // Get token and verify it
        $token = $request->query->get('token');
        if (!preg_match("/^\d{1,10}$/", $token)) {
            throw new BadRequestException('Wrong value of "token" argument.');
        }

        $thread = Thread::load($thread_id, $token);
        if (!$thread) {
            throw new BadRequestException('Wrong thread.');
        }

        $page = setup_chatview_for_user($thread);

        if ($action == 'mailthread') {
            return $this->render('mail', $page);
        } else {
            // Build js application options
            $page['chatOptions'] = json_encode($page['chat']);

            // Expand page
            return $this->render('chat', $page);
        }
    }

    protected function startChat(Request $request)
    {
        $thread = null;
        if (isset($_SESSION['threadid'])) {
            $thread = Thread::reopen($_SESSION['threadid']);
        }

        if (!$thread) {
            // Load group info
            $group_id = '';
            $group_name = '';
            $group = null;
            if (Settings::get('enablegroups') == '1') {
                $group_id = $request->query->get('group');
                if (!preg_match("/^\d{1,10}$/", $group_id)) {
                    $group_id = false;
                }

                if ($group_id) {
                    $group = group_by_id($group_id);
                    if (!$group) {
                        $group_id = false;
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
                $page['leaveMessageOptions'] = json_encode($page['leaveMessage']);

                return $this->render('chat', $page);
            }

            // Get invitation info
            if (Settings::get('enabletracking')) {
                $invitation_state = invitation_state($_SESSION['visitorid']);
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
                $page['surveyOptions'] = json_encode($page['survey']);

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
            'thread' => intval($thread->id),
            'token' => urlencode($thread->lastToken),
        );

        $chat_style_name = $request->query->get('style');
        if (preg_match("/^\w+$/", $chat_style_name)) {
            $path_args['style'] = $chat_style_name;
        }

        return $this->redirect($this->generateUrl('chat_user', $path_args));
    }
}
