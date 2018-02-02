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

namespace Mibew\Controller;

use Mibew\Database;
use Mibew\Http\Exception\BadRequestException;
use Mibew\Thread;
use Mibew\Settings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions for history-related pages.
 */
class HistoryController extends AbstractController
{
    /**
     * Generates the main history page with search and results.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $page = array();
        $operator = $this->getOperator();
        $query = $request->query->get('q', false);

        $search_type = $request->query->get('type');
        if (!in_array($search_type, array('all', 'message', 'operator', 'visitor'))) {
            $search_type = 'all';
        }

        $search_in_system_messages = ($request->query->get('insystemmessages') == 'on') || !$query;

        if ($query !== false) {
            // Escape MySQL LIKE wildcards in the query
            $escaped_query = str_replace(array('%', '_'), array('\\%', '\\_'), $query);
            // Replace commonly used "?" and "*" wildcards with MySQL ones.
            $escaped_query = str_replace(array('*', '?'), array('%', '_'), $escaped_query);

            $db = Database::getInstance();
            $groups = $db->query(
                ("SELECT {opgroup}.groupid AS groupid, vclocalname " .
                    "FROM {opgroup} " .
                    "ORDER BY vclocalname"),
                null,
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );

            $group_name = array();
            foreach ($groups as $group) {
                $group_name[$group['groupid']] = $group['vclocalname'];
            }

            $values = array(
                ':query' => "%{$escaped_query}%",
                ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
                ':invitation_not_invited' => Thread::INVITATION_NOT_INVITED,
            );

            $search_conditions = array();
            if ($search_type == 'message' || $search_type == 'all') {
                $search_conditions[] = "({message}.tmessage LIKE :query"
                    . ($search_in_system_messages
                        ? ''
                        : " AND ({message}.ikind = :kind_user OR {message}.ikind = :kind_agent)")
                    . ")";
                if (!$search_in_system_messages) {
                    $values[':kind_user'] = Thread::KIND_USER;
                    $values[':kind_agent'] = Thread::KIND_AGENT;
                }
            }
            if ($search_type == 'operator' || $search_type == 'all') {
                $search_conditions[] = "({thread}.agentname LIKE :query)";
            }
            if ($search_type == 'visitor' || $search_type == 'all') {
                $search_conditions[] = "({thread}.username LIKE :query)";
                $search_conditions[] = "({thread}.remote LIKE :query)";
            }

            // Build access condition
            $operator = $this->getOperator();

            $access = $this->buildAccessCondition($operator);
            $access_condition = $access['condition'];
            $values += $access['values'];

            // Load threads
            list($threads_count) = $db->query(
                ("SELECT COUNT(DISTINCT {thread}.dtmcreated) "
                . "FROM {thread}, {message} "
                . "WHERE {message}.threadid = {thread}.threadid "
                    . "AND ({thread}.invitationstate = :invitation_accepted "
                        . "OR {thread}.invitationstate = :invitation_not_invited) "
                    . "AND (" . implode(' OR ', $search_conditions) . ") "
                    . $access_condition),
                $values,
                array(
                    'return_rows' => Database::RETURN_ONE_ROW,
                    'fetch_type' => Database::FETCH_NUM,
                )
            );

            $pagination_info = pagination_info($threads_count);

            if ($threads_count && $pagination_info) {
                $page['pagination'] = $pagination_info;

                $limit_start = intval($pagination_info['start']);
                $limit_end = intval($pagination_info['end'] - $pagination_info['start']);

                $threads_list = $db->query(
                    ("SELECT DISTINCT {thread}.* "
                    . "FROM {thread}, {message} "
                    . "WHERE {message}.threadid = {thread}.threadid "
                        . "AND ({thread}.invitationstate = :invitation_accepted "
                            . "OR {thread}.invitationstate = :invitation_not_invited) "
                        . "AND (" . implode(' OR ', $search_conditions) . ") "
                        . $access_condition
                    . "ORDER BY {thread}.dtmcreated DESC "
                    . "LIMIT " . $limit_start . ", " . $limit_end),
                    $values,
                    array('return_rows' => Database::RETURN_ALL_ROWS)
                );

                foreach ($threads_list as $item) {
                    $thread = Thread::createFromDbInfo($item);

                    $group_name_set = ($thread->groupId
                        && $thread->groupId != 0
                        && isset($group_name[$thread->groupId]));

                    $page['pagination.items'][] = array(
                        'threadId' => $thread->id,
                        'userName' => $thread->userName,
                        'userAddress' => get_user_addr($thread->remote),
                        'agentName' => $thread->agentName,
                        'messageCount' => $thread->messageCount,
                        'groupName' => ($group_name_set
                            ? $group_name[$thread->groupId]
                            : false),
                        'chatTime' => $thread->modified - $thread->created,
                        'chatCreated' => $thread->created,
                    );
                }
            } else {
                $page['pagination'] = false;
                $page['pagination.items'] = false;
            }

            $page['formq'] = $query;
        } else {
            $page['pagination'] = false;
            $page['pagination.items'] = false;
        }

        $page['formtype'] = $search_type;
        $page['forminsystemmessages'] = $search_in_system_messages;
        $page['title'] = getlocal("Chat history");
        $page['menuid'] = "history";
        $page['canSearchInSystemMessages'] = ($search_type != 'all')
            && ($search_type != 'message');

        $page = array_merge($page, prepare_menu($operator));

        return $this->render('history', $page);
    }

    /**
     * Generates a page with thread history (thread log).
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function threadAction(Request $request)
    {
        $operator = $this->getOperator();
        $page = array();

        // Load thread info
        $thread = Thread::load($request->attributes->get('thread_id'));
        $group = group_by_id($thread->groupId);

        $thread_info = array(
            'userName' => $thread->userName,
            'userAddress' => get_user_addr($thread->remote),
            'userAgentVersion' => get_user_agent_version($thread->userAgent),
            'agentName' => $thread->agentName,
            'chatTime' => ($thread->modified - $thread->created),
            'chatStarted' => $thread->created,
            'groupName' => get_group_name($group),
        );
        $page['threadInfo'] = $thread_info;

        // Build messages list
        $last_id = -1;
        $messages = array_map(
            'sanitize_message',
            $thread->getMessages(false, $last_id)
        );

        $page['title'] = getlocal("Chat log");

        $page = array_merge($page, prepare_menu($operator, false));

        $this->getAssetManager()->attachJs('js/compiled/thread_log_app.js');
        $this->getAssetManager()->attachJs(
            sprintf(
                'jQuery(document).ready(function(){Mibew.Application.start(%s);});',
                json_encode(array('messages' => $messages))
            ),
            \Mibew\Asset\AssetManagerInterface::INLINE,
            1000
        );

        return $this->render('history_thread', $page);
    }

    /**
     * Generates a page with a user history.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function userAction(Request $request)
    {
        $operator = $this->getOperator();
        $user_id = $request->attributes->get('user_id', '');
        $page = array();

        if (!empty($user_id)) {
            $db = Database::getInstance();

            $query = "SELECT {thread}.* "
                . "FROM {thread} "
                . "WHERE userid=:user_id "
                    . "AND (invitationstate = :invitation_accepted "
                        . "OR invitationstate = :invitation_not_invited) "
                . "ORDER BY dtmcreated DESC";

            $found = $db->query(
                $query,
                array(
                    ':user_id' => $user_id,
                    ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
                    ':invitation_not_invited' => Thread::INVITATION_NOT_INVITED,
                ),
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );
        } else {
            $found = null;
        }

        $page = array_merge($page, prepare_menu($operator));

        // Setup pagination
        $pagination = setup_pagination($found, 6);
        $page['pagination'] = $pagination['info'];
        $page['pagination.items'] = $pagination['items'];

        if (!empty($page['pagination.items'])) {
            foreach ($page['pagination.items'] as $key => $item) {
                $thread = Thread::createFromDbInfo($item);
                $page['pagination.items'][$key] = array(
                    'threadId' => $thread->id,
                    'userName' => $thread->userName,
                    'userAddress' => get_user_addr($thread->remote),
                    'agentName' => $thread->agentName,
                    'chatTime' => ($thread->modified - $thread->created),
                    'chatCreated' => $thread->created,
                );
            }
        }

        $page['title'] = getlocal("Visit history");
        $page['menuid'] = "history";

        return $this->render('history_user', $page);
    }

    /**
     * Generates a page with user tracking information.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function userTrackAction(Request $request)
    {
        if (Settings::get('enabletracking') == '0') {
            throw new BadRequestException('Tracking is disabled.');
        }

        if ($request->query->has('thread')) {
            $thread_id = $request->query->get('thread');
            if (!preg_match("/^\d{1,8}$/", $thread_id)) {
                throw new BadRequestException('Wrong thread ID.');
            }
            $visitor = track_get_visitor_by_thread_id($thread_id);
            if (!$visitor) {
                throw new BadRequestException('Wrong thread.');
            }
        } else {
            $visitor_id = $request->query->get('visitor');
            if (!preg_match("/^\d{1,8}$/", $visitor_id)) {
                throw new BadRequestException('Wrong visitor ID.');
            }
            $visitor = track_get_visitor_by_id($visitor_id);
            if (!$visitor) {
                throw new BadRequestException('Wrong visitor.');
            }
        }

        $path = track_get_path($visitor);

        $page['entry'] = $visitor['entry'];
        $page['history'] = array();
        ksort($path);
        foreach ($path as $k => $v) {
            $page['history'][] = array(
                'date' => date_to_text($k),
                'link' => $v,
            );
        }

        $page['title'] = getlocal('Tracked path of visitor');
        $page['show_small_login'] = false;

        return $this->render('tracked', $page);
    }

    /**
     * Builds access condition for history select query.
     *
     * @param array $operator List of operator's fields.
     * @return array Associative array with the following keys:
     *  - "condition": string, additional condition that should be used in SQL
     *    query's where clause.
     *  - "values": array, list of additional values for placeholders.
     */
    protected function buildAccessCondition($operator)
    {
        // Administrators can view anything
        if (is_capable(CAN_ADMINISTRATE, $operator)) {
            return array(
                'condition' => '',
                'values' => array(),
            );
        }

        // Operators without "view threads" permission can view only their
        // own history.
        if (!is_capable(CAN_VIEWTHREADS, $operator)) {
            return array(
                'condition' => ' AND {thread}.agentid = :operator_id ',
                'values' => array(
                    ':operator_id' => $operator['operatorid'],
                ),
            );
        }

        // Operators who have "view threads" permission can be in isolation.
        if (in_isolation($operator)) {
            $query_conditions = array();
            $query_values = array();

            // This is not the best way of getting operators from adjacent
            // groups, but it's the only way that does not break encapsulation
            // of operators storage.
            $operators = get_operators_list(array(
                'isolated_operator_id' => $operator['operatorid'],
            ));

            $operators_placeholders = array();
            $counter = 0;
            foreach ($operators as $op) {
                $operators_placeholders[':_access_op_' . $counter] = $op['operatorid'];
                $counter++;
            }

            if (count($operators_placeholders) > 0) {
                // Make sure at least one operator was loaded.
                $operators_in_statement = implode(', ', array_keys($operators_placeholders));
                $query_conditions[] = '{thread}.agentid IN (' . $operators_in_statement . ')';
                $query_values = $operators_placeholders;
            }

            // Also the operator can view threads for the groups he belongs too.
            // These threads include ones that had no related operator but were
            // started for a specified group.
            $groups = get_groups_for_operator($operator);

            $groups_placeholders = array();
            $counter = 0;
            foreach ($groups as $group) {
                $groups_placeholders[':_access_grp_' . $counter] = $group['groupid'];
                $counter++;
            }

            if (count($groups_placeholders) > 0) {
                // Make sure at least one group was loaded.
                $groups_in_statement = implode(', ', array_keys($groups_placeholders));
                $query_conditions[] = '{thread}.groupid IN (' . $groups_in_statement . ')';
                $query_values += $groups_placeholders;
            }

            if (count($query_conditions) == 0) {
                // It seems that the does not belong to any groups or there is
                // just no another operators in adjuscent groups. Thus the
                // operator can view only his own threads.
                return array(
                    'condition' => ' AND {thread}.agentid = :operator_id ',
                    'values' => array(
                        ':operator_id' => $operator['operatorid'],
                    ),
                );
            }

            return array(
                'condition' => ' AND (' . implode(' OR ', $query_conditions) . ') ',
                'values' => $query_values,
            );
        }

        // It seems that the operator can view anything.
        return array(
            'condition' => '',
            'values' => array(),
        );
    }
}
