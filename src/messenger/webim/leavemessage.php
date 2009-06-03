<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

$email = getparam('email');
$visitor_name = getparam('name');
$message = getparam('message');
$info = getparam('info');

if( !$email ) {
	$errors[] = no_field("form.field.email");
} else if( !$visitor_name ) {
	$errors[] = no_field("form.field.name");
} else if( !$message ) {
	$errors[] = no_field("form.field.message");
} else {
	if( !is_valid_email($email)) {
		$errors[] = wrong_field("form.field.email");
	}
}

if( count($errors) > 0 ) {
	$page['formname'] = topage($visitor_name);
	$page['formemail'] = $email;
	$page['formmessage'] = topage($message);
	$page['info'] = topage($info);
	setup_logo();
	expand("styles", getchatstyle(), "leavemessage.tpl");
	exit;
}

loadsettings();
$message_locale = $settings['left_messages_locale'];
if(!locale_exists($message_locale)) {
	$message_locale = $home_locale;
}

$subject = getstring2_("leavemail.subject", array($visitor_name), $message_locale);
$body = getstring2_("leavemail.body", array($visitor_name,$email,$message,$info ? "$info\n" : ""), $message_locale);

$inbox_mail = $settings['email'];

if($inbox_mail) {
	webim_mail($inbox_mail, $email, $subject, $body);
}

setup_logo();
expand("styles", getchatstyle(), "leavemessagesent.tpl");
?>