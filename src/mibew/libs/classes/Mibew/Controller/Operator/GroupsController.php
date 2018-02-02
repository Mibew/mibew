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
 * Contains all actions which are related with operator's profile.
 */
class GroupsController extends AbstractController
{
    /**
     * Builds a page with form for edit operator's groups.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator with specified ID is not found
     *   in the system.
     */
    public function showFormAction(Request $request)
    {
        $operator = $this->getOperator();
        $operator_in_isolation = in_isolation($operator);
        $op_id = $request->attributes->getInt('operator_id');

        // Check if the target user exists
        $op = operator_by_id($op_id);
        if (!$op) {
            throw new NotFoundException('The operator is not found.');
        }

        $page = array(
            'opid' => $op_id,
            'errors' => array()
        );

        $groups = $operator_in_isolation
            ? get_groups_for_operator($operator)
            : get_all_groups();

        $can_modify = is_capable(CAN_ADMINISTRATE, $operator);

        $page['currentop'] = $op
            ? get_operator_name($op) . ' (' . $op['vclogin'] . ')'
            : getlocal('-not found-');
        $page['canmodify'] = $can_modify ? '1' : '';

        // Get IDs of groups the operator belongs to.
        $checked_groups = array();
        if ($op) {
            $checked_groups = get_operator_group_ids($op_id);
        }

        // Get all available groups
        $page['groups'] = array();
        foreach ($groups as $group) {
            $group['vclocalname'] = $group['vclocalname'];
            $group['vclocaldescription'] = $group['vclocaldescription'];
            $group['checked'] = in_array($group['groupid'], $checked_groups);

            $page['groups'][] = $group;
        }

        $page['stored'] = $request->query->has('stored');
        $page['title'] = getlocal('Operator groups');
        $page['menuid'] = ($operator['operatorid'] == $op_id) ? 'profile' : 'operators';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('operator_groups', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\Operator\GroupsController::showFormAction()}
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
        $operator_in_isolation = in_isolation($operator);
        $op_id = $request->attributes->getInt('operator_id');

        // Check if the target operator exists
        $op = operator_by_id($op_id);
        if (!$op) {
            throw new NotFoundException('The operator is not found.');
        }

        // Get all groups that are available for the target operator.
        $groups = $operator_in_isolation
            ? get_groups_for_operator($operator)
            : get_all_groups();

        // Build list of operator's new groups.
        $new_groups = array();
        foreach ($groups as $group) {
            if ($request->request->get('group' . $group['groupid']) == 'on') {
                $new_groups[] = $group['groupid'];
            }
        }

        // Update operator's group and redirect the current operator to the same
        // page using GET method.
        update_operator_groups($op['operatorid'], $new_groups);
        $redirect_to = $this->generateUrl(
            'operator_groups',
            array(
                'operator_id' => $op_id,
                'stored' => true,
            )
        );

        return $this->redirect($redirect_to);
    }
}
