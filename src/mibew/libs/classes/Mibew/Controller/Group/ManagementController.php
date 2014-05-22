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

namespace Mibew\Controller\Group;

use Mibew\Database;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operators' groups management.
 */
class ManagementController extends AbstractController
{
    /**
     * Generates list of all available groups.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
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
     * @param Request $request Incoming request.
     * @return string Rendered page content.
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
}
