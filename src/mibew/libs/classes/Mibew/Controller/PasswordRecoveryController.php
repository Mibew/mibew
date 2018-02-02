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

namespace Mibew\Controller;

use Mibew\Http\Exception\BadRequestException;
use Mibew\Mail\Template as MailTemplate;
use Mibew\Mail\Utils as MailUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Contains actions realted with password recovery procedure.
 */
class PasswordRecoveryController extends AbstractController
{
    /**
     * Generates a page for the first step of password recovery process.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        if ($this->getOperator()) {
            // If the operator is logged in just redirect him to the home page.
            return $this->redirect($request->getUriForPath('/operator'));
        }

        $page = array(
            'version' => MIBEW_VERSION,
            'title' => getlocal('Trouble Accessing Your Account?'),
            'headertitle' => getlocal('Mibew Messenger'),
            'show_small_login' => true,
            'fixedwrap' => true,
            'errors' => array(),
        );
        $login_or_email = '';

        if ($request->isMethod('POST')) {
            // When HTTP GET method is used the form is just rendered but the
            // user does not pass any data. Thus we need to prevent CSRF attacks
            // only for POST requests
            csrf_check_token($request);
        }

        if ($request->isMethod('POST') && $request->request->has('loginoremail')) {
            $login_or_email = $request->request->get('loginoremail');

            $to_restore = MailUtils::isValidAddress($login_or_email)
                ? operator_by_email($login_or_email)
                : operator_by_login($login_or_email);
            if (!$to_restore) {
                $page['errors'][] = getlocal('No such Operator');
            }

            $email = $to_restore['vcemail'];
            if (count($page['errors']) == 0 && !MailUtils::isValidAddress($email)) {
                $page['errors'][] = "Operator hasn't set his e-mail";
            }

            if (count($page['errors']) == 0) {
                $token = sha1($to_restore['vclogin'] . (function_exists('openssl_random_pseudo_bytes')
                    ? openssl_random_pseudo_bytes(32)
                    : (time() + microtime()) . mt_rand(0, 99999999)));

                // Update the operator
                $to_restore['dtmrestore'] = time();
                $to_restore['vcrestoretoken'] = $token;
                update_operator($to_restore);

                $href = $this->getRouter()->generate(
                    'password_recovery_reset',
                    array(
                        'id' => $to_restore['operatorid'],
                        'token' => $token,
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                // Load mail templates and substitute placeholders there.
                $mail_template = MailTemplate::loadByName('password_recovery', get_current_locale());
                if (!$mail_template) {
                    throw new \RuntimeException('Cannot load "password_recovery" mail template');
                }

                $this->sendMail(MailUtils::buildMessage(
                    $email,
                    $email,
                    $mail_template->buildSubject(),
                    $mail_template->buildBody(array(
                        get_operator_name($to_restore),
                        $href,
                    ))
                ));
                $page['isdone'] = true;

                return $this->render('password_recovery', $page);
            }
        }

        $page['formloginoremail'] = $login_or_email;
        $page['localeLinks'] = get_locale_links();
        $page['isdone'] = false;

        return $this->render('password_recovery', $page);
    }

    /**
     * Resets operators password and provides an ability to set the new one.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function resetAction(Request $request)
    {
        $page = array(
            'version' => MIBEW_VERSION,
            'showform' => true,
            'title' => getlocal('Change your password'),
            'headertitle' => getlocal('Mibew Messenger'),
            'show_small_login' => true,
            'fixedwrap' => true,
            'errors' => array(),
        );

        if ($request->isMethod('POST')) {
            // When HTTP GET method is used the form is just rendered but the
            // user does not pass any data. Thus we need to prevent CSRF attacks
            // only for POST requests
            csrf_check_token($request);
        }

        // Make sure user id is specified and its format is correct.
        $op_id = $request->isMethod('GET')
            ? $request->query->get('id')
            : $request->request->get('id');
        if (!preg_match("/^\d{1,9}$/", $op_id)) {
            throw new BadRequestException();
        }

        // Make sure token is specified and its format is correct.
        $token = $request->isMethod('GET')
            ? $request->query->get('token')
            : $request->request->get('token');
        if (!preg_match("/^[\dabcdef]+$/", $token)) {
            throw new BadRequestException();
        }

        $operator = operator_by_id($op_id);

        if (!$operator) {
            $page['errors'][] = 'No such operator';
            $page['showform'] = false;
        } elseif ($token != $operator['vcrestoretoken']) {
            $page['errors'][] = 'Wrong token';
            $page['showform'] = false;
        }

        if (count($page['errors']) == 0 && $request->isMethod('POST') && $request->request->has('password')) {
            $password = $request->request->get('password');
            $password_confirm = $request->request->get('passwordConfirm');

            if (!$password) {
                $page['errors'][] = no_field('Password');
            }

            if ($password != $password_confirm) {
                $page['errors'][] = getlocal('Entered passwords do not match');
            }

            if (count($page['errors']) == 0) {
                $page['isdone'] = true;

                // Update the operator
                $operator['vcrestoretoken'] = '';
                $operator['vcpassword'] = calculate_password_hash($operator['vclogin'], $password);
                update_operator($operator);

                $page['loginname'] = $operator['vclogin'];

                return $this->render('password_recovery_reset', $page);
            }
        }

        $page['id'] = $op_id;
        $page['token'] = $token;
        $page['isdone'] = false;

        return $this->render('password_recovery_reset', $page);
    }
}
