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

function log_notification($locale, $kind, $to, $subj, $text, $refop, $link)
{
	global $mysqlprefix;
	$query = sprintf(
		"insert into ${mysqlprefix}chatnotification (locale,vckind,vcto,vcsubject,tmessage,refoperator,dtmcreated) values ('%s','%s','%s','%s','%s',%s,%s)",
		mysql_real_escape_string($locale, $link),
		mysql_real_escape_string($kind, $link),
		mysql_real_escape_string($to, $link),
		mysql_real_escape_string($subj, $link),
		mysql_real_escape_string($text, $link),
		$refop ? intval($refop) : "0",
		"CURRENT_TIMESTAMP");

	perform_query($query, $link);
}

function mibew_mail($toaddr, $reply_to, $subject, $body, $link)
{
	global $mibew_encoding, $mibew_mailbox, $mail_encoding, $current_locale;

	$headers = "From: $mibew_mailbox\r\n"
			   . "Reply-To: " . myiconv($mibew_encoding, $mail_encoding, $reply_to) . "\r\n"
			   . "Content-Type: text/plain; charset=$mail_encoding\r\n"
			   . 'X-Mailer: PHP/' . phpversion();

	$real_subject = "=?" . $mail_encoding . "?B?" . base64_encode(myiconv($mibew_encoding, $mail_encoding, $subject)) . "?=";

	$body = preg_replace("/\n/", "\r\n", $body);

	log_notification($current_locale, "mail", $toaddr, $subject, $body, null, $link);

	$old_from = ini_get('sendmail_from');
	ini_set('sendmail_from', $mibew_mailbox);
	@mail($toaddr, $real_subject, wordwrap(myiconv($mibew_encoding, $mail_encoding, $body), 70), $headers);
	if (isset($old_from)) {
		ini_set('sendmail_from', $old_from);
	}
}

function mibew_xmpp($toaddr, $subject, $text, $link)
{
	global $current_locale;
	log_notification($current_locale, "xmpp", $toaddr, $subject, $text, null, $link);
}

?>