<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require('libs/common.php');
require('libs/chat.php');

$token = verifyparam( "token", "/^\d{1,8}$/");
$threadid = verifyparam( "thread", "/^\d{1,8}$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

$mail = getparam('email');
$page['email'] = $mail;

// TODO check email

$history = "";
$lastid = -1;
$output = get_messages( $threadid,"text",true,$lastid );
foreach( $output as $msg ) {
	$history .= $msg;
}

$subject = getstring("mail.user.history.subject");
$body = getstring2("mail.user.history.body", array($thread['userName'],$history) ); 

$headers = 'From: '.$webim_from_email."\r\n" .
   'Reply-To: '.$webim_from_email."\r\n" .
   'X-Mailer: PHP/'.phpversion();

mail($mail,$subject,wordwrap($body,70),$headers);

start_html_output();
require('view/chat_mailsent.php');

?>