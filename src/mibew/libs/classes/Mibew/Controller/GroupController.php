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
use Mibew\Http\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operators' groups.
 */
class GroupController extends AbstractController
{
    /**
     * Generates list of all available groups.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        set_csrf_token();

        $operator = $request->attributes->get('_operator');
        $page = array(
            'errors' => array(),
        );

        $sort_by = $request->query->get('sortby');
        if (!in_array($sort_by, array('name', 'lastseen', 'weight'))) {
            $sort_by = 'name';
        }

        $sort['by'] = $sort_by;
        $sort['desc'] = ($request->query->get('sortdirection', 'desc') == 'desc');

        // Load and prepare groups
        $groups = get_sorted_groups($sort);
        foreach ($groups as &$group) {
            $group['vclocalname'] = $group['vclocalname'];
            $group['vclocaldescription'] = $group['vclocaldescription'];
            $group['isOnline'] = group_is_online($group);
            $group['isAway'] = group_is_away($group);
            $group['lastTimeOnline'] = time() - ($group['ilastseen'] ? $group['ilastseen'] : time());
            $group['inumofagents'] = $group['inumofagents'];
        }
        unset($group);

        // Set values that are needed to build sorting block.
        $page['groups'] = $groups;
        $page['formsortby'] = $sort['by'];
        $page['formsortdirection'] = $sort['desc'] ? 'desc' : 'asc';
        $page['canmodify'] = is_capable(CAN_ADMINISTRATE, $operator);
        $page['availableOrders'] = array(
            array('id' => 'name', 'name' => getlocal('form.field.groupname')),
            array('id' => 'lastseen', 'name' => getlocal('page_agents.status')),
            array('id' => 'weight', 'name' => getlocal('page.groups.weight')),
        );
        $page['availableDirections'] = array(
            array('id' => 'desc', 'name' => getlocal('page.groups.sortdirection.desc')),
            array('id' => 'asc', 'name' => getlocal('page.groups.sortdirection.asc')),
        );

        // Set other variables and render the response.
        $page['title'] = getlocal('page.groups.title');
        $page['menuid'] = 'groups';
        $page = array_merge($page, prepare_menu($operator));

        return $this->render('groups', $page);
    }

    /**
     * Removes a group from the database.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function deleteAction(Request $request)
    {
        csrf_check_token($request);

        $db = Database::getInstance();

        // Remove the group and all its relations.
        $group_id = $request->attributes->getInt('group_id');
        $db->query("DELETE FROM {chatgroup} WHERE groupid = ?", array($group_id));
        $db->query("DELETE FROM {chatgroupoperator} WHERE groupid = ?", array($group_id));
        $db->query("UPDATE {chatthread} SET groupid = 0 WHERE groupid = ?", array($group_id));

        // Redirect user to canned messages list. Use only "sortby" and
        // "sortdirection" get params for the target URL.
        $parameters = array_intersect_key(
            $request->query->all(),
            array_flip(array('sortby', 'sortdirection'))
        );

        return $this->redirect($this->generateUrl('groups', $parameters));
    }

    /**
     * Builds a page with members edit form.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function showMembersFormAction(Request $request)
    {
        set_csrf_token();

        $operator = $request->attributes->get('_operator');
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
        $checked_operators = array();
        foreach (get_group_members($group_id) as $rel) {
            $checked_operators[] = $rel['operatorid'];
        }

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
        $page['title'] = getlocal('page.groupmembers.title');
        $page['menuid'] = 'groups';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('group_members', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\GroupController::showMembersFormAction()} method.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function submitMembersFormAction(Request $request)
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

    /**
     * Builds a page with form for add/edit group.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function showEditFormAction(Request $request)
    {
        set_csrf_token();

        $operator = $request->attributes->get('_operator');
        $group_id = $request->attributes->getInt('group_id', false);

        $page = array(
            'gid' => false,
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitEditForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        if ($group_id) {
            // Check if the group exisits
            $group = group_by_id($group_id);
            if (!$group) {
                throw new NotFoundException('The group is not found.');
            }

            // Set form values
            $page['formname'] = $group['vclocalname'];
            $page['formdescription'] = $group['vclocaldescription'];
            $page['formcommonname'] = $group['vccommonname'];
            $page['formcommondescription'] = $group['vccommondescription'];
            $page['formemail'] = $group['vcemail'];
            $page['formweight'] = $group['iweight'];
            $page['formparentgroup'] = $group['parent'];
            $page['grid'] = $group['groupid'];
            $page['formtitle'] = $group['vctitle'];
            $page['formchattitle'] = $group['vcchattitle'];
            $page['formhosturl'] = $group['vchosturl'];
            $page['formlogo'] = $group['vclogo'];
        }

        // Override group's fields from the request if it's needed. This
        // case will take place when save handler fails.
        if ($request->isMethod('POST')) {
            $page['formname'] = $request->request->get('name');
            $page['formdescription'] = $request->request->get('description');
            $page['formcommonname'] = $request->request->get('commonname');
            $page['formcommondescription'] = $request->request->get('commondescription');
            $page['formemail'] = $request->request->get('email');
            $page['formweight'] = $request->request->get('weight');
            $page['formparentgroup'] = $request->request->get('parentgroup');
            $page['formtitle'] = $request->request->get('title');
            $page['formchattitle'] = $request->request->get('chattitle');
            $page['formhosturl'] = $request->request->get('hosturl');
            $page['formlogo'] = $request->request->get('logo');
        }

        // Set other page variables and render the template.
        $page['stored'] = $request->query->has('stored');
        $page['availableParentGroups'] = get_available_parent_groups($group_id);
        $page['formaction'] = $request->getBaseUrl() . $request->getPathInfo();
        $page['title'] = getlocal('page.group.title');
        $page['menuid'] = 'groups';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('group_edit', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\GroupController::showEditFormAction()} method.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function submitEditFormAction(Request $request)
    {
        csrf_check_token($request);

        $errors = array();

        // Use value from the form and not from the path to make sure it is
        // correct. If not, treat the param as empty one.
        $group_id = $request->request->get('gid', false);
        if (!preg_match("/^\d{1,10}$/", $group_id)) {
            $group_id = false;
        }

        $parent_group = $request->request->get('parentgroup');
        if (!$parent_group || !preg_match("/^\d{1,10}$/", $parent_group)) {
            $parent_group = null;
        }

        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $common_name = $request->request->get('commonname');
        $common_description = $request->request->get('commondescription');
        $email = $request->request->get('email');
        $weight = $request->request->get('weight');
        $title = $request->request->get('title');
        $chat_title = $request->request->get('chattitle');
        $host_url = $request->request->get('hosturl');
        $logo = $request->request->get('logo');

        if (!$name) {
            $errors[] = no_field("form.field.groupname");
        }

        if ($email != '' && !is_valid_email($email)) {
            $errors[] = wrong_field("form.field.mail");
        }

        if (!preg_match("/^(\d{1,10})?$/", $weight)) {
            $errors[] = wrong_field("form.field.groupweight");
        }

        if (!$weight) {
            $weight = 0;
        }

        $existing_group = group_by_name($name);
        $duplicate_name = (!$group_id && $existing_group)
            || ($group_id
                && $existing_group
                && $group_id != $existing_group['groupid']);

        if ($duplicate_name) {
            $errors[] = getlocal("page.group.duplicate_name");
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showEditFormAction($request);
        }

        if (!$group_id) {
            // Greate new group
            $new_dep = create_group(array(
                'name' => $name,
                'description' => $description,
                'commonname' => $common_name,
                'commondescription' => $common_description,
                'email' => $email,
                'weight' => $weight,
                'parent' => $parent_group,
                'title' => $title,
                'chattitle' => $chat_title,
                'hosturl' => $host_url,
                'logo' => $logo));

            // Redirect an operator to group's member page.
            $redirect_to = $this->generateUrl(
                'group_members',
                array('group_id' => (int)$new_dep['groupid'])
            );
        } else {
            // Update exisitng group
            update_group(array(
                'id' => $group_id,
                'name' => $name,
                'description' => $description,
                'commonname' => $common_name,
                'commondescription' => $common_description,
                'email' => $email,
                'weight' => $weight,
                'parent' => $parent_group,
                'title' => $title,
                'chattitle' => $chat_title,
                'hosturl' => $host_url,
                'logo' => $logo));

            // Redirect an operator to group's page.
            $redirect_to = $this->generateUrl(
                'group_edit',
                array('group_id' => $group_id)
            );
        }

        return $this->redirect($redirect_to);
    }

    /**
     * Builds list of the group tabs.
     *
     * @param Request $request Current request.
     * @return array Tabs list. The keys of the array are tabs titles and the
     *   values are tabs URLs.
     */
    protected function buildTabs(Request $request)
    {
        $tabs = array();
        $route = $request->attributes->get('_route');
        $group_id = $request->attributes->get('group_id', false);
        $args = array('group_id' => $group_id);

        if ($group_id) {
            $tabs[getlocal('page_group.tab.main')] = ($route == 'group_members')
                ? $this->generateUrl('group_edit', $args)
                : '';

            $tabs[getlocal('page_group.tab.members')] = ($route != 'group_members')
                ? $this->generateUrl('group_members', $args)
                : '';
        }

        return $tabs;
    }
}
