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

require_once('libs/common.php');
require_once('libs/chat.php');
require_once('libs/expand.php');
require_once('libs/notify.php');

$errors = array();
$page = array();

$token = verifyparam( "token", "/^\d{1,10}$/");
$threadid = verifyparam( "thread", "/^\d{1,10}$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

$email = getparam('email');
$page['email'] = $email;

if( !$email ) {
	$errors[] = no_field("form.field.email");
} else if( !is_valid_email($email)) {
	$errors[] = wrong_field("form.field.email");
}

if( count($errors) > 0 ) {
	$page['formemail'] = $email;
	$page['ct.chatThreadId'] = $thread['threadid'];
	$page['ct.token'] = $thread['ltoken'];
	$page['level'] = "";
	setup_logo();
	expand("styles", getchatstyle(), "mail.tpl");
	exit;
}

$history = "";
$lastid = -1;
$output = get_messages( $threadid,"text",true,$lastid );
foreach( $output as $msg ) {
	$history .= $msg;
}

$subject = getstring("mail.user.history.subject", true);
$body = getstring2("mail.user.history.body", array($thread['userName'],$history), true);

$link = connect();
mibew_mail($email, $mibew_mailbox, $subject, $body, $link);
mysql_close($link);

setup_logo();
expand("styles", getchatstyle(), "mailsent.tpl");
exit;
?>