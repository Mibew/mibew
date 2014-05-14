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

function mibew_mail($to_addr, $reply_to, $subject, $body)
{
    global $mibew_mailbox;

    $headers = "From: $mibew_mailbox\r\n"
        . "Reply-To: " . $reply_to . "\r\n"
        . "Content-Type: text/plain; charset=utf-8\r\n"
        . 'X-Mailer: PHP/' . phpversion();

    $real_subject = "=?utf-8?B?" . base64_encode($subject) . "?=";

    $body = preg_replace("/\n/", "\r\n", $body);

    $old_from = ini_get('sendmail_from');
    @ini_set('sendmail_from', $mibew_mailbox);
    @mail(
        $to_addr,
        $real_subject,
        wordwrap($body, 70),
        $headers
    );
    if (isset($old_from)) {
        @ini_set('sendmail_from', $old_from);
    }
}
