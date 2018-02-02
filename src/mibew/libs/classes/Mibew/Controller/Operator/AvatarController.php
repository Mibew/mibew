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

use Mibew\Settings;
use Mibew\Http\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operator's avatar.
 */
class AvatarController extends AbstractController
{
    /**
     * Builds a page with form for edit operator's avatar.
     *
     * @param Request $request incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function showFormAction(Request $request)
    {
        $operator = $this->getOperator();
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

        $page['avatar'] = $op['vcavatar'] ? $this->asset($op['vcavatar']) : '';
        $page['currentop'] = $op
            ? get_operator_name($op) . ' (' . $op['vclogin'] . ')'
            : getlocal('-not found-');
        $page['canmodify'] = $can_modify ? '1' : '';
        $page['title'] = getlocal('Upload photo');
        $page['menuid'] = ($operator['operatorid'] == $op_id) ? 'profile' : 'operators';

        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('operator_avatar', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\Operator\AvatarController::showFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $operator = $this->getOperator();
        $op_id = $request->attributes->getInt('operator_id');
        $errors = array();

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
                $errors[] = failed_uploading_file($orig_filename, "Uploaded file size exceeded");
            } elseif (!in_array($ext, $valid_types)) {
                $errors[] = failed_uploading_file($orig_filename, "Invalid file type");
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
                    $avatar = 'files/avatar/' . $new_file_name;
                } catch (Exception $e) {
                    $errors[] = failed_uploading_file($orig_filename, "Error moving file");
                }
            }
        } else {
            $errors[] = getlocal("No file selected");
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showFormAction($request);
        }

        // Update path to avatar in the database
        update_operator_avatar($op['operatorid'], $avatar);

        // Operator's data are cached in the authentication manager thus we need
        // to update them manually.
        if ($avatar && $operator['operatorid'] == $op_id) {
            $operator['vcavatar'] = $avatar;
            $this->getAuthenticationManager()->setOperator($operator);
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
     */
    public function deleteAction(Request $request)
    {
        csrf_check_token($request);

        $operator = $this->getOperator();
        $op_id = $request->attributes->getInt('operator_id');

        // Try to load the target operator.
        if (!operator_by_id($op_id)) {
            throw new NotFoundException('The operator is not found');
        }

        // Try to remove the current operator's avatar if it exists.
        $current_avatar = $operator['vcavatar'];
        if ($current_avatar) {
            @unlink(MIBEW_FS_ROOT . '/files/avatar/' . basename($current_avatar));
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
