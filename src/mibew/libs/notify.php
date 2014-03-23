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

/**
 * Send an email
 *
 * @param string $to_addr Comma separated list recipient emails
 * @param string $reply_to Comma separated list replies emails
 * @param string $subject subject of email
 * @param string $body text of email.
 */
function mibew_mail($to_addr, $reply_to, $subject, $body)
{
    global $mibew_mailbox, $mail_encoding;

    $headers = "From: $mibew_mailbox\r\n"
        . "Reply-To: " . myiconv(MIBEW_ENCODING, $mail_encoding, $reply_to) . "\r\n"
        . "Content-Type: text/plain; charset=$mail_encoding\r\n"
        . 'X-Mailer: PHP/' . phpversion();

    $real_subject = "=?" . $mail_encoding . "?B?"
        . base64_encode(myiconv(MIBEW_ENCODING, $mail_encoding, $subject)) . "?=";

    $body = preg_replace("/\n/", "\r\n", $body);

    $old_from = ini_get('sendmail_from');
    @ini_set('sendmail_from', $mibew_mailbox);
    @mail(
        $to_addr,
        $real_subject,
        wordwrap(myiconv(MIBEW_ENCODING, $mail_encoding, $body), 70),
        $headers
    );
    if (isset($old_from)) {
        @ini_set('sendmail_from', $old_from);
    }
}
