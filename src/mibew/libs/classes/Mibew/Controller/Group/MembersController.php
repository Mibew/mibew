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

namespace Mibew\Controller\Group;

use Mibew\Http\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operators' groups.
 */
class MembersController extends AbstractController
{
    /**
     * Builds a page with members edit form.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator's group with specified ID is
     *   not found in the system.
     */
    public function showFormAction(Request $request)
    {
        $operator = $this->getOperator();
        $group_id = $request->attributes->getInt('group_id');

        $page = array(
            'groupid' => $group_id,
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitMembersForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        $operators = get_operators_list();
        $group = group_by_id($group_id);

        // Check if the group exists
        if (!$group) {
            throw new NotFoundException('The group is not found.');
        }

        $page['formop'] = array();
        $page['currentgroup'] = $group
            ? htmlspecialchars($group['vclocalname'])
            : '';

        // Get list of group's members
        $checked_operators = get_group_members($group_id);

        // Prepare the list of all operators
        $page['operators'] = array();
        foreach ($operators as $op) {
            $op['vclocalename'] = $op['vclocalename'];
            $op['vclogin'] = $op['vclogin'];
            $op['checked'] = in_array($op['operatorid'], $checked_operators);

            $page['operators'][] = $op;
        }

        // Set other values and render the page
        $page['stored'] = $request->query->get('stored');
        $page['title'] = getlocal('Members');
        $page['menuid'] = 'groups';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('group_members', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\GroupController::showMembersFormAction()} method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator's group with specified ID is
     *   not found in the system.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $operators = get_operators_list();
        $group_id = $request->attributes->getInt('group_id');
        $group = group_by_id($group_id);

        // Check if specified group exists
        if (!$group) {
            throw new NotFoundException('The group is not found.');
        }

        // Update members list
        $new_members = array();
        foreach ($operators as $op) {
            if ($request->request->get('op' . $op['operatorid']) == 'on') {
                $new_members[] = $op['operatorid'];
            }
        }
        update_group_members($group_id, $new_members);

        // Redirect opeartor to group members page.
        $parameters = array(
            'group_id' => $group_id,
            'stored' => true,
        );

        return $this->redirect($this->generateUrl('group_members', $parameters));
    }
}
