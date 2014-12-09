<?php
/*
 * This file is a part of Mibew Messenger.
 *
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

namespace Mibew\Mail;

/**
 * Contains a set of utility methods related with emails.
 */
class Utils
{
    /**
     * Builds an instance of \Swift_message.
     *
     * @param string $to_addr Address the message should be send to.
     * @param string $reply_to The address which will be used in "Reply-to"
     *   mail header.
     * @param string $subject Subject of the message.
     * @param string $body Body of the message.
     * @return \Swift_Message
     */
    public static function buildMessage($to_addr, $reply_to, $subject, $body)
    {
        return \Swift_Message::newInstance()
            ->setContentType('text/plain')
            ->setCharset('utf-8')
            ->setMaxLineLength(70)
            ->setFrom(MIBEW_MAILBOX)
            ->setTo($to_addr)
            ->setReplyTo($reply_to)
            ->setSubject($subject)
            ->setBody(preg_replace("/\n/", "\r\n", $body));
    }

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
