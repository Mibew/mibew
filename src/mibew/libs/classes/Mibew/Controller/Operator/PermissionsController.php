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
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operator's permissions.
 */
class PermissionsController extends AbstractController
{
    /**
     * Builds a page with form for edit operator's permissions.
     *
     * @param Request $request Incoming request.
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
            'canmodify' => is_capable(CAN_ADMINISTRATE, $operator) ? '1' : '',
            'errors' => array(),
        );

        if ($op_id === $operator['operatorid']) {
            $this->getAssetManager()->attachJs('js/compiled/operator_permissions.js');
        }

        $op = operator_by_id($op_id);
        if (!$op) {
            throw new NotFoundException('The operator is not found.');
        }

        // Check if the target operator exists
        $page['currentop'] = $op
            ? get_operator_name($op) . ' (' . $op['vclogin'] . ')'
            : getlocal('-not found-');

        // Build list of permissions which belongs to the target operator.
        $checked_permissions = array();
        foreach (permission_ids() as $perm => $id) {
            if (is_capable($perm, $op)) {
                $checked_permissions[] = $id;
            }
        }

        // Build list of all available permissions
        $page['permissionsList'] = array();
        foreach (get_permission_list() as $perm) {
            $perm['checked'] = in_array($perm['id'], $checked_permissions);
            $page['permissionsList'][] = $perm;
        }

        $page['stored'] = $request->query->has('stored');
        $page['title'] = getlocal('Permissions');
        $page['menuid'] = ($operator['operatorid'] == $op_id) ? 'profile' : 'operators';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('operator_permissions', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\Operator\PermissionsController::showFormAction()}
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

        // Check if the target operator exists
        $op = operator_by_id($op_id);
        if (!$op) {
            throw new NotFoundException('The operator is not found.');
        }

        $new_permissions = isset($op['iperm']) ? $op['iperm'] : 0;

        foreach (permission_ids() as $perm => $id) {
            if ($request->request->get('permissions' . $id) == 'on') {
                $new_permissions |= (1 << $perm);
            } else {
                $new_permissions &= ~(1 << $perm);
            }
        }

        // Update operator's permissions in the database and in cached
        // authentication manager data if it is needed.
        update_operator_permissions($op['operatorid'], $new_permissions);

        if ($operator['operatorid'] == $op_id) {
            $operator['iperm'] = $new_permissions;
            $this->getAuthenticationManager()->setOperator($operator);
        }

        // Redirect the current operator to the same page using GET method.
        $redirect_to = $this->generateUrl(
            'operator_permissions',
            array(
                'operator_id' => $op_id,
                'stored' => true,
            )
        );

        return $this->redirect($redirect_to);
    }
}
