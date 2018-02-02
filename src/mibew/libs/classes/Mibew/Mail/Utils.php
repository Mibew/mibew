<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
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

use Symfony\Component\Yaml\Parser as YamlParser;
use True\Punycode;

/**
 * Contains a set of utility methods related with emails.
 */
class Utils
{
    /**
     * Checks if the passed in e-mail address is valid.
     *
     * The method play nice with addresses that have national characters in the
     * domain part.
     *
     * @param string $address E-mail address to check.
     * @return boolean
     */
    public static function isValidAddress($address)
    {
        // Email address can contain UTF8 characters in the domain part, but
        // PHP's validator does not allow IDN. Thus address normalization is
        // used here to convert domain part of the address to punycode.
        $normalized_address = self::normalizeAddress($address);

        return (bool)filter_var($normalized_address, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Builds an instance of \Swift_message.
     *
     * The method assumes that $to_addr and $reply_to arguments are valid email
     * addresses. One can use {@link Utils::isValidAddress} for address
     * validation.
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
            ->setTo(self::normalizeAddress($to_addr))
            ->setReplyTo(self::normalizeAddress($reply_to))
            ->setSubject($subject)
            ->setBody(preg_replace("/\n/", "\r\n", $body));
    }

    /**
     * Imports mail templates from a YAML file.
     *
     * @param string $locale Locale code.
     * @param string $file Full path to the file that should be imported.
     */
    public static function importTemplates($locale, $file)
    {
        // Get new mail templates
        $parser = new YamlParser();
        $new_templates = $parser->parse(file_get_contents($file));
        if (empty($new_templates)) {
            // Nothing to import.
            return;
        }

        foreach ($new_templates as $name => $template_info) {
            // Validate the template
            $is_valid_template = is_array($template_info)
                && array_key_exists('subject', $template_info)
                && array_key_exists('body', $template_info);
            if (!$is_valid_template) {
                throw new \RuntimeException(sprintf(
                    'An invalid mail template "%s" is found in "%s".',
                    $name,
                    $file
                ));
            }

            if (!Template::loadByName($name, $locale, true)) {
                // Import only templates that are not already in the database.
                $template = new Template($name, $locale);
                $template->subject = $template_info['subject'];
                $template->body = $template_info['body'];
                $template->save();
            }
        }
    }

    /**
     * Converts domain part of the address to punycode if needed.
     *
     * @param string $address The address that should be normalized.
     * @return string
     */
    private static function normalizeAddress($address)
    {
        $chunks = explode('@', $address);
        if (count($chunks) < 2) {
            // The address has no "@" character thus it's not a real email
            // address and should not be normalized at all.
            return $address;
        }

        $punycode = new Punycode();
        // Domain part should be converted to punycode to play nice with IDN.
        $domain = $punycode->encode(array_pop($chunks));
        // Local part should be left as is.
        $local_part = implode('@', $chunks);

        return $local_part . '@' . $domain;
    }

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
