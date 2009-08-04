<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
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

webim_mail($email, $webim_mailbox, $subject, $body);

setup_logo();
expand("styles", getchatstyle(), "mailsent.tpl");
exit;
?>