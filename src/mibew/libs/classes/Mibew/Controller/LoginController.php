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

use Symfony\Component\HttpFoundation\Request;

/**
 * Contains acctions related with operator login process.
 */
class LoginController extends AbstractController
{
    /**
     * Builds a page with login form.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function showFormAction(Request $request)
    {
        // Check if the operator already logged in
        if ($this->getOperator()) {
            // Redirect the operator to home page.
            // TODO: Use a route for URI generation.
            return $this->redirect($request->getUriForPath('/operator'));
        }

        $page = array(
            'formisRemember' => true,
            'version' => MIBEW_VERSION,
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        // Try to get login from the request.
        if ($request->request->has('login')) {
            $page['formlogin'] = $request->request->get('login');
        } elseif ($request->query->has('login')) {
            $login = $request->query->get('login');
            if (preg_match("/^(\w{1,15})$/", $login)) {
                $page['formlogin'] = $login;
            }
        }

        $page['localeLinks'] = get_locale_links();
        $page['title'] = getlocal('Login');
        $page['headertitle'] = getlocal('Mibew Messenger');
        $page['show_small_login'] = false;
        $page['fixedwrap'] = true;

        return $this->render('login', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\LoginController::showFormAction()} method.
     *
     * Triggers 'operatorLogin' event after operator logged in and pass to it an
     * associative array with following items:
     *  - 'operator': array of the logged in operator info;
     *  - 'remember': boolean, indicates if system should remember operator.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $login = $request->request->get('login');
        $password = $request->request->get('password');
        $remember = $request->request->get('isRemember') == 'on';
        $errors = array();

        $operator = operator_by_login($login);
        $operator_can_login = $operator
            && isset($operator['vcpassword'])
            && check_password_hash($operator['vclogin'], $password, $operator['vcpassword'])
            && !operator_is_disabled($operator);

        if ($operator_can_login) {
            // Login the operator to the system
            $this->getAuthenticationManager()->loginOperator($operator, $remember);

            // Redirect the current operator to the needed page.
            $target = isset($_SESSION[SESSION_PREFIX . 'backpath'])
                ? $_SESSION[SESSION_PREFIX . 'backpath']
                : $request->getUriForPath('/operator');

            return $this->redirect($target);
        } else {
            if (operator_is_disabled($operator)) {
                $errors[] = getlocal('Your account is temporarily blocked. Please contact system administrator.');
            } else {
                $errors[] = getlocal("Entered login/password is incorrect");
            }
        }

        // Rebuild login form
        $request->attributes->set('errors', $errors);

        return $this->showFormAction($request);
    }
}
