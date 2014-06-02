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
 * Contains actions which are related with send chat history to user mail.
 */
class MailController extends AbstractController
{
    /**
     * Process sending chat history to an email.
     *
     * @param Request $request Incoming request.
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse Rendered
     *   page content or a redirect response.
     * @throws BadRequestException If the thread cannot be loaded by some
     * reasons.
     */
    public function indexAction(Request $request)
    {
        $page = array(
            'errors' => array(),
        );

        // Get and validate thread id
        $thread_id = $request->request->get('thread');
        if (!preg_match("/^\d{1,10}$/", $thread_id)) {
            throw new BadRequestException('Wrong value of "thread" argument.');
        }

        // Get token and verify it
        $token = $request->request->get('token');
        if (!preg_match("/^\d{1,10}$/", $token)) {
            throw new BadRequestException('Wrong value of "token" argument.');
        }

        $thread = Thread::load($thread_id, $token);
        if (!$thread) {
            throw new BadRequestException('Wrong thread.');
        }

        $email = $request->request->get('email');
        $page['email'] = $email;
        $group = is_null($thread->groupId) ? null : group_by_id($thread->groupId);
        if (!$email) {
            $page['errors'][] = no_field('form.field.email');
        } elseif (!is_valid_email($email)) {
            $page['errors'][] = wrong_field('form.field.email');
        }

        if (count($page['errors']) > 0) {
            $page['formemail'] = $email;
            $page['chat.thread.id'] = $thread->id;
            $page['chat.thread.token'] = $thread->lastToken;
            $page['level'] = '';
            $page = array_merge_recursive(
                $page,
                setup_logo($group)
            );

            return $this->render('mail', $page);
        }

        $history = '';
        $last_id = -1;
        $messages = $thread->getMessages(true, $last_id);
        foreach ($messages as $msg) {
            $history .= message_to_text($msg);
        }

        $subject = getstring('mail.user.history.subject', true);
        $body = getstring2(
            'mail.user.history.body',
            array($thread->userName,
                $history,
                Settings::get('title'),
                Settings::get('hosturl')
            ),
            true
        );

        mibew_mail($email, MIBEW_MAILBOX, $subject, $body);

        $page = array_merge_recursive($page, setup_logo($group));

        return $this->render('mailsent', $page);
    }
}
