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

namespace Mibew\Controller;

use Mibew\Database;
use Mibew\Http\Exception\BadRequestException;
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
        if ($request->attributes->get('_operator')) {
            // If the operator is logged in just redirect him to the home page.
            return $this->redirect($request->getUriForPath('/operator'));
        }

        $page = array(
            'version' => MIBEW_VERSION,
            'title' => getlocal('restore.title'),
            'headertitle' => getlocal('app.title'),
            'show_small_login' => true,
            'fixedwrap' => true,
            'errors' => array(),
        );
        $login_or_email = '';

        if ($request->request->has('loginoremail')) {
            $login_or_email = $request->request->get('loginoremail');

            $to_restore = is_valid_email($login_or_email)
                ? operator_by_email($login_or_email)
                : operator_by_login($login_or_email);
            if (!$to_restore) {
                $page['errors'][] = getlocal('no_such_operator');
            }

            $email = $to_restore['vcemail'];
            if (count($page['errors']) == 0 && !is_valid_email($email)) {
                $page['errors'][] = "Operator hasn't set his e-mail";
            }

            if (count($page['errors']) == 0) {
                $token = sha1($to_restore['vclogin'] . (function_exists('openssl_random_pseudo_bytes')
                    ? openssl_random_pseudo_bytes(32)
                    : (time() + microtime()) . mt_rand(0, 99999999)));

                $db = Database::getInstance();
                $db->query(
                    ("UPDATE {chatoperator} "
                        . "SET dtmrestore = :now, vcrestoretoken = :token "
                        . "WHERE operatorid = :operatorid"),
                    array(
                        ':now' => time(),
                        ':token' => $token,
                        ':operatorid' => $to_restore['operatorid'],
                    )
                );

                $href = $this->getRouter()->generate(
                    'password_recovery_reset',
                    array(
                        'id' => $to_restore['operatorid'],
                        'token' => $token,
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                mibew_mail(
                    $email,
                    $email,
                    getstring('restore.mailsubj'),
                    getstring2(
                        'restore.mailtext',
                        array(get_operator_name($to_restore), $href)
                    )
                );
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
            'title' => getlocal('resetpwd.title'),
            'headertitle' => getlocal('app.title'),
            'show_small_login' => true,
            'fixedwrap' => true,
            'errors' => array(),
        );

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

        if (count($page['errors']) == 0 && $request->request->has('password')) {
            $password = $request->request->get('password');
            $password_confirm = $request->request->get('passwordConfirm');

            if (!$password) {
                $page['errors'][] = no_field('form.field.password');
            }

            if ($password != $password_confirm) {
                $page['errors'][] = getlocal('my_settings.error.password_match');
            }

            if (count($page['errors']) == 0) {
                $page['isdone'] = true;

                $db = Database::getInstance();
                $db->query(
                    ("UPDATE {chatoperator} "
                        . "SET vcpassword = ?, vcrestoretoken = '' "
                        . "WHERE operatorid = ?"),
                    array(
                        calculate_password_hash($operator['vclogin'], $password),
                        $op_id,
                    )
                );
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
