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
use Mibew\Database;
use Mibew\Http\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operators management.
 */
class ManagementController extends AbstractController
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
}
