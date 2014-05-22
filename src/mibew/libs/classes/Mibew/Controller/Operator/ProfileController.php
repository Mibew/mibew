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

namespace Mibew\Controller\Operator;

use Mibew\Controller\AbstractController;
use Mibew\Http\Exception\AccessDeniedException;
use Mibew\Http\Exception\NotFoundException;
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
        set_csrf_token();

        $operator = $request->attributes->get('_operator');
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
                $page['errors'][] = getlocal('my_settings.error.no_password');
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

        // Operator without CAN_ADMINISTRATE permission can neither create new
        // operators nor view/edit other operator's profile.
        $access_restricted = !is_capable(CAN_ADMINISTRATE, $operator)
            && (!$op_id || ($operator['operatorid'] != $op_id));
        if ($access_restricted) {
            throw new AccessDeniedException();
        }

        $can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
            || is_capable(CAN_ADMINISTRATE, $operator);

        $page['stored'] = $request->query->has('stored');
        $page['canmodify'] = $can_modify ? '1' : '';
        $page['canchangelogin'] = is_capable(CAN_ADMINISTRATE, $operator);
        $page['needChangePassword'] = check_password_hash($operator['vclogin'], '', $operator['vcpassword']);
        $page['title'] = getlocal('page_agent.title');
        $page['menuid'] = ($op_id == $operator['operatorid']) ? 'profile' : 'operators';
        $page['requirePassword'] = (!$op_id || $page['needChangePassword']);
        $page['formaction'] = $request->getBaseUrl() . $request->getPathInfo();
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = setup_operator_settings_tabs($op_id, 0);

        return $this->render('operator_edit', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\OperatorController::showEditFormAction()} method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws AccessDeniedException If the current operator has no rights to
     *   modify choosen profile.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $errors = array();
        $operator = $request->attributes->get('_operator');
        // Use value from the form and not from the path to make sure it is
        // correct. If not, treat the param as empty one.
        $op_id = $request->request->getInt('opid', false);

        if (is_capable(CAN_ADMINISTRATE, $operator)) {
            $login = $request->request->get('login');
        } else {
            $login = $operator['vclogin'];
        }

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $password_confirm = $request->request->get('passwordConfirm');
        $local_name = $request->request->get('name');
        $common_name = $request->request->get('commonname');
        $code = $request->request->get('code');

        $can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
            || is_capable(CAN_ADMINISTRATE, $operator);
        if (!$can_modify) {
            throw new AccessDeniedException('Cannot modify profile.');
        }

        if (!$local_name) {
            $errors[] = no_field('form.field.agent_name');
        }

        if (!$common_name) {
            $errors[] = no_field('form.field.agent_commonname');
        }

        if (!$login) {
            $errors[] = no_field('form.field.login');
        } elseif (!preg_match("/^[\w_\.]+$/", $login)) {
            $errors[] = getlocal('page_agent.error.wrong_login');
        }

        if (!$email || !is_valid_email($email)) {
            $errors[] = wrong_field('form.field.mail');
        }

        if ($code && (!preg_match("/^[A-Za-z0-9_]+$/", $code))) {
            $errors[] = getlocal('page_agent.error.wrong_agent_code');
        }

        if (!$op_id && !$password) {
            $errors[] = no_field('form.field.password');
        }

        if ($password != $password_confirm) {
            $errors[] = getlocal('my_settings.error.password_match');
        }

        $existing_operator = operator_by_login($login);
        $duplicate_login = (!$op_id && $existing_operator)
            || ($op_id
                && $existing_operator
                && $op_id != $existing_operator['operatorid']);
        if ($duplicate_login) {
            $errors[] = getlocal('page_agent.error.duplicate_login');
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
            $errors[] = getlocal('page_agent.error.duplicate_email');
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

        // Update existing operator
        update_operator($op_id, $login, $email, $password, $local_name, $common_name, $code);

        // Operator data are cached in the session, thus we need to manually
        // update them.
        if (!empty($password) && $op_id == $operator['operatorid']) {
            // Check if the admin has set his password for the first time.
            $to_dashboard = check_password_hash($login, '', $operator['vcpassword']) && $password != '';

            // Update operator's password.
            $operator['vcpassword'] = calculate_password_hash($login, $password);
            $_SESSION[SESSION_PREFIX . 'operator'] = $operator;
            $request->attributes->set('_operator', $operator);

            // Redirect the admin to the home page if needed.
            if ($to_dashboard) {
                return $this->redirect($request->getBasePath() . '/operator/index.php');
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
