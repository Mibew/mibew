<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2010 Mibew Messenger Community
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

function webim_mail($toaddr, $reply_to, $subject, $body, $link)
{
	global $webim_encoding, $webim_mailbox, $mail_encoding, $current_locale;

	$headers = "From: $webim_mailbox\r\n"
			   . "Reply-To: " . myiconv($webim_encoding, $mail_encoding, $reply_to) . "\r\n"
			   . "Content-Type: text/plain; charset=$mail_encoding\r\n"
			   . 'X-Mailer: PHP/' . phpversion();

	$real_subject = "=?" . $mail_encoding . "?B?" . base64_encode(myiconv($webim_encoding, $mail_encoding, $subject)) . "?=";

	$body = preg_replace("/\n/", "\r\n", $body);

	@mail($toaddr, $real_subject, wordwrap(myiconv($webim_encoding, $mail_encoding, $body), 70), $headers);
}

?>