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

// Import namespaces and classes of the core
use Mibew\Settings;
use Mibew\Thread;
use Mibew\Style\ChatStyle;

// Initialize libraries
require_once(dirname(__FILE__) . '/libs/init.php');

$page = array(
    'errors' => array(),
);

$token = verify_param("token", "/^\d{1,8}$/");
$thread_id = verify_param("thread", "/^\d{1,8}$/");

$thread = Thread::load($thread_id, $token);
if (!$thread) {
    die("wrong thread");
}

// Initialize chat style which is currently used in system
$chat_style = new ChatStyle(ChatStyle::getCurrentStyle());

$email = get_param('email');
$page['email'] = $email;
$group = is_null($thread->groupId) ? null : group_by_id($thread->groupId);
if (!$email) {
    $page['errors'][] = no_field("form.field.email");
} elseif (!is_valid_email($email)) {
    $page['errors'][] = wrong_field("form.field.email");
}

if (count($page['errors']) > 0) {
    $page['formemail'] = $email;
    $page['chat.thread.id'] = $thread->id;
    $page['chat.thread.token'] = $thread->lastToken;
    $page['level'] = "";
    $page = array_merge_recursive(
        $page,
        setup_logo($group)
    );
    $chat_style->render('mail', $page);
    exit;
}

$history = "";
$last_id = -1;
$messages = $thread->getMessages(true, $last_id);
foreach ($messages as $msg) {
    $history .= message_to_text($msg);
}

$subject = getstring("mail.user.history.subject", true);
$body = getstring2(
    "mail.user.history.body",
    array($thread->userName,
        $history,
        Settings::get('title'),
        Settings::get('hosturl')
    ),
    true
);

mibew_mail($email, $mibew_mailbox, $subject, $body);

$page = array_merge_recursive($page, setup_logo($group));

$chat_style->render('mailsent', $page);
