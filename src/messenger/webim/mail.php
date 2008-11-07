<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('libs/common.php');
require_once('libs/chat.php');
require_once('libs/expand.php');

$errors = array();
$page = array();

$token = verifyparam( "token", "/^\d{1,8}$/");
$threadid = verifyparam( "thread", "/^\d{1,8}$/");

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

$subject = getstring("mail.user.history.subject");
$body = getstring2("mail.user.history.body", array($thread['userName'],$history) );

webim_mail($email, $webim_from_email, $subject, $body);

setup_logo();
expand("styles", getchatstyle(), "mailsent.tpl");
?>