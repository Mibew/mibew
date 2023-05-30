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

namespace Mibew\Controller\Chat;

use Mibew\Http\Exception\AccessDeniedException;
use Mibew\Http\Exception\NotFoundException;
use Mibew\Mail\Template as MailTemplate;
use Mibew\Mail\Utils as MailUtils;
use Mibew\Settings;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions which are related with send chat history to user mail.
 */
class MailController extends AbstractController
{
    /**
     * Renders the mail form.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the thread with specified ID and token is
     * not found.
     */
    public function showFormAction(Request $request)
    {

        // Check whether this feature is enabled at all
        if (!Settings::get('usercansendemail')) {
            throw new AccessDeniedException();
        }

        $page = array(
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitForm" action.
            'errors' => $request->attributes->get('errors', array()),
            // Setup CAPTCHA if needed
            'showCaptcha' => Settings::get('enableemailcaptcha'),
        );

        $thread_id = $request->attributes->get('thread_id');
        $token = $request->attributes->get('token');

        // We have to check that the thread is owned by the user.
        $is_own_thread = isset($_SESSION[SESSION_PREFIX . 'own_threads'])
            && in_array($thread_id, $_SESSION[SESSION_PREFIX . 'own_threads']);

        // Try to load the thread
        $thread = Thread::load($thread_id, $token);
        if (!$thread || !$is_own_thread) {
            throw new NotFoundException('The thread is not found.');
        }

        $email = $request->request->get('email', '');
        $group = $thread->groupId ? group_by_id($thread->groupId) : null;

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

    /**
     * Process submitting of the mail form.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the thread with specified ID and token is
     * not found.
     */
    public function submitFormAction(Request $request)
    {

        // Check whether this feature is enabled at all
        if (!Settings::get('usercansendemail')) {
            throw new AccessDeniedException();
        }

        $errors = array();

        $thread_id = $request->attributes->get('thread_id');
        $token = $request->attributes->get('token');

        // We have to check that the thread is owned by the user.
        $is_own_thread = isset($_SESSION[SESSION_PREFIX . 'own_threads'])
            && in_array($thread_id, $_SESSION[SESSION_PREFIX . 'own_threads']);

        // Try to load the thread
        $thread = Thread::load($thread_id, $token);
        if (!$thread || !$is_own_thread) {
            throw new NotFoundException('The thread is not found.');
        }

        // Check email
        $email = $request->request->get('email');
        if (!$email) {
            $errors[] = no_field('Your email');
        } elseif (!MailUtils::isValidAddress($email)) {
            $errors[] = wrong_field('Your email');
        }

        // Check captcha
        if (Settings::get('enableemailcaptcha') == '1' && can_show_captcha()) {
            $captcha = $request->request->get('captcha');
            $original = isset($_SESSION[SESSION_PREFIX . 'mibew_captcha'])
                ? $_SESSION[SESSION_PREFIX . 'mibew_captcha']
                : '';
            unset($_SESSION[SESSION_PREFIX . 'mibew_captcha']);
            if (empty($original) || empty($captcha) || $captcha != $original) {
                $errors[] = 'The letters you typed don\'t match the letters that were shown in the picture.';
            }
        }

        if (count($errors) > 0) {
            $request->attributes->set('errors', $errors);

            // Render the mail form again
            return $this->showFormAction($request);
        }

        $history = '';
        $last_id = -1;
        $messages = $thread->getMessages(true, $last_id);
        foreach ($messages as $msg) {
            $history .= message_to_text($msg);
        }

        // Load mail templates and substitute placeholders there.
        $mail_template = MailTemplate::loadByName('user_history', get_current_locale());
        if ($mail_template) {
            $this->sendMail(MailUtils::buildMessage(
                $email,
                MIBEW_MAILBOX,
                $mail_template->buildSubject(),
                $mail_template->buildBody(array(
                    $thread->userName,
                    $history,
                    Settings::get('title'),
                    Settings::get('hosturl'),
                ))
            ));
        } else {
            trigger_error(
                'Cannot send e-mail because "user_history" mail template cannot be loaded.',
                E_USER_WARNING
            );
        }

        $group = $thread->groupId ? group_by_id($thread->groupId) : null;
        $page = setup_logo($group);
        $page['email'] = $email;

        return $this->render('mailsent', $page);
    }
}
