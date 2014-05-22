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
use Mibew\Settings;
use Mibew\Http\Exception\AccessDeniedException;
use Mibew\Http\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operators.
 */
class OperatorController extends AbstractController
{
    /**
     * Generates list of all operators in the system.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function indexAction(Request $request)
    {
        set_csrf_token();
        setlocale(LC_TIME, getstring('time.locale'));

        $operator = $request->attributes->get('_operator');
        $page = array(
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitMembersForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        $sort['by'] = $request->query->get('sortby');
        if (!in_array($sort['by'], array('login', 'commonname', 'localename', 'lastseen'))) {
            $sort['by'] = 'login';
        }

        $sort['desc'] = ($request->query->get('sortdirection', 'desc') == 'desc');

        $page['formsortby'] = $sort['by'];
        $page['formsortdirection'] = $sort['desc'] ? 'desc' : 'asc';
        $list_options['sort'] = $sort;
        if (in_isolation($operator)) {
            $list_options['isolated_operator_id'] = $operator['operatorid'];
        }

        $operators_list = get_operators_list($list_options);

        // Prepare operator to render in template
        foreach ($operators_list as &$item) {
            $item['vclogin'] = $item['vclogin'];
            $item['vclocalename'] = $item['vclocalename'];
            $item['vccommonname'] = $item['vccommonname'];
            $item['isAvailable'] = operator_is_available($item);
            $item['isAway'] = operator_is_away($item);
            $item['lastTimeOnline'] = time() - $item['time'];
            $item['isDisabled'] = operator_is_disabled($item);
        }
        unset($item);

        $page['allowedAgents'] = $operators_list;
        $page['canmodify'] = is_capable(CAN_ADMINISTRATE, $operator);
        $page['availableOrders'] = array(
            array('id' => 'login', 'name' => getlocal('page_agents.login')),
            array('id' => 'localename', 'name' => getlocal('page_agents.agent_name')),
            array('id' => 'commonname', 'name' => getlocal('page_agents.commonname')),
            array('id' => 'lastseen', 'name' => getlocal('page_agents.status')),
        );
        $page['availableDirections'] = array(
            array('id' => 'desc', 'name' => getlocal('page_agents.sortdirection.desc')),
            array('id' => 'asc', 'name' => getlocal('page_agents.sortdirection.asc')),
        );

        $page['title'] = getlocal('page_agents.title');
        $page['menuid'] = 'operators';
        $page = array_merge($page, prepare_menu($operator));

        return $this->render('operators', $page);
    }

    /**
     * Removes an operator from the database.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function deleteAction(Request $request)
    {
        csrf_check_token($request);

        $current_operator = $request->attributes->get('_operator');
        $operator_id = $request->attributes->getInt('operator_id');
        $errors = array();

        if ($operator_id == $current_operator['operatorid']) {
            $errors[] = getlocal('page_agents.error.cannot_remove_self');
        } else {
            $operator = operator_by_id($operator_id);
            if (!$operator) {
                throw new NotFoundException('The operator is not found.');
            } elseif ($operator['vclogin'] == 'admin') {
                $errors[] = getlocal("page_agents.error.cannot_remove_admin");
            }
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The operator cannot be removed by some reasons. Just rebuild
            // index page and show errors there.
            return $this->indexAction($request);
        }

        // Remove the operator and redirect the current operator.
        delete_operator($operator_id);

        return $this->redirect($this->generateUrl('operators'));
    }

    /**
     * Disables an operator.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function disableAction(Request $request)
    {
        csrf_check_token($request);

        $current_operator = $request->attributes->get('_operator');
        $operator_id = $request->attributes->getInt('operator_id');
        $errors = array();

        if ($operator_id == $current_operator['operatorid']) {
            $errors[] = getlocal('page_agents.cannot.disable.self');
        } else {
            $operator = operator_by_id($operator_id);
            if (!$operator) {
                throw new NotFoundException('The operator is not found.');
            } elseif ($operator['vclogin'] == 'admin') {
                $errors[] = getlocal('page_agents.cannot.disable.admin');
            }
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The operator cannot be removed by some reasons. Just rebuild
            // index page and show errors there.
            return $this->indexAction($request);
        }

        // Disable the operator
        $db = Database::getInstance();
        $db->query(
            "update {chatoperator} set idisabled = ? where operatorid = ?",
            array('1', $operator_id)
        );

        // Redirect the current operator to the page with operators list
        return $this->redirect($this->generateUrl('operators'));
    }

    /**
     * Enables an operator.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function enableAction(Request $request)
    {
        csrf_check_token($request);

        $operator_id = $request->attributes->getInt('operator_id');

        if (!operator_by_id($operator_id)) {
            throw new NotFoundException('The operator is not found.');
        }

        $db = Database::getInstance();
        $db->query(
            "update {chatoperator} set idisabled = ? where operatorid = ?",
            array('0', $operator_id)
        );

        // Redirect the current operator to the page with operators list
        return $this->redirect($this->generateUrl('operators'));
    }

    /**
     * Builds a page with form for add/edit operator.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function showEditFormAction(Request $request)
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
    public function submitEditFormAction(Request $request)
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
            return $this->showEditFormAction($request);
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

    /**
     * Builds a page with form for edit operator's avatar.
     *
     * @param Request $request incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function showAvatarFormAction(Request $request)
    {
        set_csrf_token();

        $operator = $request->attributes->get('_operator');
        $op_id = $request->attributes->get('operator_id');
        $page = array(
            'opid' => $op_id,
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitAvatarForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        $can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
            || is_capable(CAN_ADMINISTRATE, $operator);

        // Try to load the target operator.
        $op = operator_by_id($op_id);
        if (!$op) {
            throw new NotFoundException('The operator is not found');
        }

        $page['avatar'] = $op['vcavatar'];
        $page['currentop'] = $op
            ? get_operator_name($op) . ' (' . $op['vclogin'] . ')'
            : getlocal('not_found');
        $page['canmodify'] = $can_modify ? '1' : '';
        $page['title'] = getlocal('page_avatar.title');
        $page['menuid'] = ($operator['operatorid'] == $op_id) ? 'profile' : 'operators';

        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = setup_operator_settings_tabs($op_id, 1);

        return $this->render('operator_avatar', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\OperatorController::showAvatarFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     * @throws AccessDeniedException If the current operator has no rights to
     *   modify choosen profile.
     */
    public function submitAvatarFormAction(Request $request)
    {
        csrf_check_token($request);

        $operator = $request->attributes->get('_operator');
        $op_id = $request->attributes->getInt('operator_id');
        $errors = array();

        $can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
            || is_capable(CAN_ADMINISTRATE, $operator);
        if (!$can_modify) {
            throw new AccessDeniedException('Cannot modify avatar.');
        }

        // Try to load the target operator.
        $op = operator_by_id($op_id);
        if (!$op) {
            throw new NotFoundException('The operator is not found');
        }

        $avatar = $op['vcavatar'];
        $file = $request->files->get('avatarFile');

        if ($file) {
            // Process uploaded file.
            $valid_types = array("gif", "jpg", "png", "tif", "jpeg");

            $ext = $file->getClientOriginalExtension();
            $orig_filename = $file->getClientOriginalName();
            $new_file_name = intval($op_id) . ".$ext";
            $file_size = $file->getSize();

            if ($file_size == 0 || $file_size > Settings::get('max_uploaded_file_size')) {
                $errors[] = failed_uploading_file($orig_filename, "errors.file.size.exceeded");
            } elseif (!in_array($ext, $valid_types)) {
                $errors[] = failed_uploading_file($orig_filename, "errors.invalid.file.type");
            } else {
                // Remove avatar if it already exists
                $avatar_local_dir = MIBEW_FS_ROOT . '/files/avatar/';
                $full_file_path = $avatar_local_dir . $new_file_name;
                if (file_exists($full_file_path)) {
                    unlink($full_file_path);
                }

                // Move uploaded file to avatar directory
                try {
                    $file->move($avatar_local_dir, $new_file_name);
                    $avatar = MIBEW_WEB_ROOT . "/files/avatar/$new_file_name";
                } catch (Exception $e) {
                    $errors[] = failed_uploading_file($orig_filename, "errors.file.move.error");
                }
            }
        } else {
            $errors[] = "No file selected";
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showAvatarFormAction($request);
        }

        // Update path to avatar in the database
        update_operator_avatar($op['operatorid'], $avatar);

        // Operator's data are cached in the session thus we need to update them
        // manually.
        if ($avatar && $operator['operatorid'] == $op_id) {
            $operator['vcavatar'] = $avatar;

            $_SESSION[SESSION_PREFIX . 'operator'] = $operator;
            $request->attributes->set('_operator', $operator);
        }

        // Redirect the operator to the same page using GET method.
        $redirect_to = $this->generateUrl(
            'operator_avatar',
            array('operator_id' => $op_id)
        );

        return $this->redirect($redirect_to);
    }

    /**
     * Removes operator's avatar from the database.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     * @throws AccessDeniedException If the current operator has no rights to
     *   modify choosen profile.
     */
    public function deleteAvatarAction(Request $request)
    {
        csrf_check_token($request);

        $operator = $request->attributes->get('_operator');
        $op_id = $request->attributes->getInt('operator_id');

        $can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
            || is_capable(CAN_ADMINISTRATE, $operator);
        if (!$can_modify) {
            throw new AccessDeniedException('Cannot modify avatar.');
        }

        // Try to load the target operator.
        if (!operator_by_id($op_id)) {
            throw new NotFoundException('The operator is not found');
        }

        // Update avatar value in database
        update_operator_avatar($op_id, '');

        // Redirect the current operator to the same page using GET method.
        $redirect_to = $this->generateUrl(
            'operator_avatar',
            array('operator_id' => $op_id)
        );

        return $this->redirect($redirect_to);
    }
}
