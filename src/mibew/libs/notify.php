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

use Mibew\Database;
use Symfony\Component\Yaml\Parser as YamlParser;

function mibew_mail($to_addr, $reply_to, $subject, $body)
{
    $headers = "From: " . MIBEW_MAILBOX . "\r\n"
        . "Reply-To: " . $reply_to . "\r\n"
        . "Content-Type: text/plain; charset=utf-8\r\n"
        . 'X-Mailer: PHP/' . phpversion();

    $real_subject = "=?utf-8?B?" . base64_encode($subject) . "?=";

    $body = preg_replace("/\n/", "\r\n", $body);

    $old_from = ini_get('sendmail_from');
    @ini_set('sendmail_from', MIBEW_MAILBOX);
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

/**
 * Loads an email template.
 *
 * @param string $name Machine name of the template.
 * @param string $locale Locale code for the mail template.
 * @param boolean $strict If it is set to true no fall back to default locale
 *   will be made.
 * @return array|boolean Associative array with localized mail template data. If
 *   the template with specified locale is not found a template for default
 *   locale will be loaded. If the last one does not found too boolean FALSE
 *   will be returned.
 *   Mail template array contains the following keys:
 *     - templateid: int, internale id of the template. It should not be used
 *       directly.
 *     - name: string, machine name of the template.
 *     - locale: string, locale code the templates belongs to.
 *     - title: string, localized human-readable mail template title.
 *     - subject: string, localized value which will be used as subject field in
 *       an email.
 *     - body: string, localized value which will be used as body in an email.
 */
function mail_template_load($name, $locale, $strict = false)
{
    static $templates = array();

    if (!isset($templates[$locale][$name])) {
        // Try to load the template from the database.
        $template = Database::getInstance()->query(
            "SELECT * FROM {mailtemplate} WHERE name = :name AND locale = :locale",
            array(
                ':name' => $name,
                ':locale' => $locale,
            ),
            array(
                'return_rows' => Database::RETURN_ONE_ROW,
            )
        );

        if (!$template) {
            if ($strict) {
                return false;
            }

            // There is no template in the database.
            if ($locale == DEFAULT_LOCALE) {
                // The template is still not found.
                $template = false;
            } else {
                // Try to load the template for the default locale.
                $template = mail_template_load($name, DEFAULT_LOCALE);
            }
        }

        $templates[$locale][$name] = $template;
    }

    return $templates[$locale][$name];
}

/**
 * Saves a mail template to the database.
 *
 * @param string $name Machine name of the template to save.
 * @param string $locale Locale code the template belongs to.
 * @param string $subject Localized string that is used as email subject.
 * @param string $body Localized string that is used as email body.
 */
function mail_template_save($name, $locale, $subject, $body)
{
    $db = Database::getInstance();
    $template = mail_template_load($name, $locale);

    if ($template && $template['locale'] == $locale) {
        // Update existing mail template
        $db->query(
            ("UPDATE {mailtemplate} "
                . "SET subject = :subject, body = :body "
                . "WHERE templateid = :id"),
            array(
                ':id' => $template['templateid'],
                ':subject' => $subject,
                ':body' => $body,
            )
        );
    } else {
        // Insert a new mail template
        $db->query(
            ("INSERT INTO {mailtemplate} (name, locale, subject, body) "
                . "VALUES (:name, :locale, :subject, :body)"),
            array(
                ':name' => $name,
                ':locale' => $locale,
                ':subject' => $subject,
                ':body' => $body,
            )
        );
    }
}

/**
 * Import mail templates from a YAML file.
 *
 * @param string $locale Locale code.
 * @param string $file Full path to the file that should be imported.
 */
function import_mail_templates($locale, $file)
{
    // Get new mail templates
    $parser = new YamlParser();
    $new_templates = $parser->parse(file_get_contents($file));
    if (empty($new_templates)) {
        // Nothing to import.
        return;
    }

    foreach ($new_templates as $name => $template) {
        // Validate the template
        $is_valid_template = is_array($template)
            && array_key_exists('subject', $template)
            && array_key_exists('body', $template);
        if (!$is_valid_template) {
            throw new \RuntimeException(sprintf(
                'An invalid mail template "%s" is found in "%s".',
                $name,
                $file
            ));
        }

        if (!mail_template_load($name, $locale, true)) {
            // Import only templates that are not already in the database.
            mail_template_save(
                $name,
                $locale,
                $template['subject'],
                $template['body']
            );
        }
    }
}
