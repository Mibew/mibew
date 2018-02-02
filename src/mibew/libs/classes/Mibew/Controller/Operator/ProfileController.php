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

namespace Mibew\Controller\Operator;

use Mibew\Http\Exception\NotFoundException;
use Mibew\Mail\Utils as MailUtils;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operator's profile.
 */
class ProfileController extends AbstractController
{
    /**
     * Builds a page with form for add/edit operator.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function showFormAction(Request $request)
    {
        $operator = $this->getOperator();
        $page = array(
            'opid' => false,
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitEditForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        $op_id = false;

        if ($request->attributes->has('operator_id')) {
            // Load and validate an operator to edit
            $op_id = $request->attributes->getInt('operator_id');
            $op = operator_by_id($op_id);
            if (!$op) {
                throw new NotFoundException('The operator is not found.');
            }

            // Show an error if the admin password hasn't been set yet.
            $no_password = check_password_hash($operator['vclogin'], '', $operator['vcpassword'])
                && !$request->query->has('stored');
            if ($no_password) {
                $page['errors'][] = getlocal('No Password set for the Administrator');
            }

            $page['formlogin'] = $op['vclogin'];
            $page['formname'] = $op['vclocalename'];
            $page['formemail'] = $op['vcemail'];
            $page['formcommonname'] = $op['vccommonname'];
            $page['formcode'] = $op['code'];
            $page['opid'] = $op['operatorid'];
        }

        // Override group's fields from the request if it's needed. This
        // case will take place when a save handler fails and passes the request
        // to this action.
        if ($request->isMethod('POST')) {
            // The login field can be disabled in the form. In that case it will
            // not has a value. Thus we should override login field only when it
            // is set.
            if ($request->request->has('login')) {
                $page['formlogin'] = $request->request->get('login');
            }

            $page['formname'] = $request->request->get('name');
            $page['formemail'] = $request->request->get('email');
            $page['formcommonname'] = $request->request->get('commonname');
            $page['formcode'] = $request->request->get('code');
        }

        $can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
            || is_capable(CAN_ADMINISTRATE, $operator);

        $page['stored'] = $request->query->has('stored');
        $page['canmodify'] = $can_modify ? '1' : '';
        // The login cannot be changed for existing operators because it will
        // make the stored password hash invalid.
        $page['canchangelogin'] = is_capable(CAN_ADMINISTRATE, $operator) && !$op_id;
        $page['title'] = getlocal('Operator details');
        $page['menuid'] = ($op_id == $operator['operatorid']) ? 'profile' : 'operators';
        $page['requirePassword'] = (!$op_id);
        $page['formaction'] = $request->getBaseUrl() . $request->getPathInfo();
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('operator_edit', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\OperatorController::showEditFormAction()} method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $errors = array();
        $operator = $this->getOperator();
        $op_id = $request->attributes->getInt('operator_id');
        $login = $request->request->get('login');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $password_confirm = $request->request->get('passwordConfirm');
        $local_name = $request->request->get('name');
        $common_name = $request->request->get('commonname');
        $code = $request->request->get('code');

        if (!$local_name) {
            $errors[] = no_field('Name');
        }

        if (!$common_name) {
            $errors[] = no_field('International name (Latin)');
        }

        // The login is needed only for new operators. If login is changed for
        // existing operator the stored password hash becomes invalid.
        if (!$op_id) {
            if (!$login) {
                $errors[] = no_field('Login');
            } elseif (!preg_match("/^[\w_\.]+$/", $login)) {
                $errors[] = getlocal('Login should contain only latin characters, numbers and underscore symbol.');
            }
        }

        if (!$email || !MailUtils::isValidAddress($email)) {
            $errors[] = wrong_field('E-mail');
        }

        if ($code && (!preg_match("/^[A-Za-z0-9_]+$/", $code))) {
            $errors[] = getlocal('Code should contain only latin characters, numbers and underscore symbol.');
        }

        if (!$op_id && !$password) {
            $errors[] = no_field('Password');
        }

        if ($password != $password_confirm) {
            $errors[] = getlocal('Entered passwords do not match');
        }

        $existing_operator = operator_by_login($login);
        $duplicate_login = (!$op_id && $existing_operator)
            || ($op_id
                && $existing_operator
                && $op_id != $existing_operator['operatorid']);
        if ($duplicate_login) {
            $errors[] = getlocal('Please choose another login because an operator with that login is already registered in the system.');
        }

        // Check if operator with specified email already exists in the database.
        $existing_operator = operator_by_email($email);
        $duplicate_email =
            // Create operator with email already in database.
            (!$op_id && $existing_operator)
            // Update operator email to existing one.
            || ($op_id
                && $existing_operator
                && $op_id != $existing_operator['operatorid']);
        if ($duplicate_email) {
            $errors[] = getlocal('Please choose another email because an operator with that email is already registered in the system.');
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showFormAction($request);
        }

        if (!$op_id) {
            // Create new operator and redirect the current operator to avatar
            // page.
            $new_operator = create_operator($login, $email, $password, $local_name, $common_name, '', $code);
            $redirect_to = $this->generateUrl(
                'operator_avatar',
                array('operator_id' => $new_operator['operatorid'])
            );

            return $this->redirect($redirect_to);
        }

        // Mix old operator's fields with updated values
        $target_operator = array(
            'vcemail' => $email,
            'vclocalename' => $local_name,
            'vccommonname' => $common_name,
            'code' => $code,
        ) + operator_by_id($op_id);
        // Set the password only if it's not an empty string.
        if ($password !== '') {
            $target_operator['vcpassword'] = calculate_password_hash($target_operator['vclogin'], $password);
        }
        // Update operator's fields in the database.
        update_operator($target_operator);

        // Operator's data are cached in the authentication manager, thus we need
        // to manually update them.
        if ($target_operator['operatorid'] == $operator['operatorid']) {
            // Check if the admin has set his password for the first time.
            $to_dashboard = check_password_hash($operator['vclogin'], '', $operator['vcpassword']) && $password != '';

            // Update operator's fields.
            $this->getAuthenticationManager()->setOperator($target_operator);

            // Redirect the admin to the home page if needed.
            if ($to_dashboard) {
                return $this->redirect($this->generateUrl('home_operator'));
            }
        }

        // Redirect the operator to edit page again to use GET method instead of
        // POST.
        $redirect_to = $this->generateUrl(
            'operator_edit',
            array(
                'operator_id' => $op_id,
                'stored' => true,
            )
        );

        return $this->redirect($redirect_to);
    }
}
