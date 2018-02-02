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

namespace Mibew\RequestProcessor;

// Import namespaces and classes of the core
use Mibew\Authentication\AuthenticationManagerAwareInterface;
use Mibew\Authentication\AuthenticationManagerInterface;
use Mibew\Ban;
use Mibew\Database;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Settings;
use Mibew\Thread;
use Mibew\API\API as MibewAPI;
use Mibew\RequestProcessor\Exception\UsersProcessorException;

/**
 * Incapsulates awaiting users list API related functions.
 */
class UsersProcessor extends ClientSideProcessor implements AuthenticationManagerAwareInterface
{
    /**
     * @var AuthenticationManagerInterface|null
     */
    protected $authenticationManager = null;

    /**
     * {@inheritdoc}
     */
    public function setAuthenticationManager(AuthenticationManagerInterface $manager)
    {
        $this->authenticationManager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * Class constructor
     */
    protected function __construct()
    {
        parent::__construct(array(
            'signature' => '',
            'trusted_signatures' => array(''),
            'event_prefix' => 'users'
        ));
    }

    /**
     * Creates and returns an instance of the \Mibew\API\API class.
     *
     * @return \Mibew\API\API
     */
    protected function getMibewAPIInstance()
    {
        return MibewAPI::getAPI('\\Mibew\\API\\Interaction\\UsersInteraction');
    }

    /**
     * Sends asynchronous request
     *
     * @param array $request The 'request' array. See Mibew API for details
     * @return boolean true on success or false on failure
     */
    protected function sendAsyncRequest($request)
    {
        // Define empty agent id
        $agent_id = null;
        foreach ($request['functions'] as $function) {
            // Save agent id from first function in package
            if (is_null($agent_id)) {
                $agent_id = $function['arguments']['agentId'];
                continue;
            }
            // Check agent id for the remaining functions
            if ($agent_id != $function['arguments']['agentId']) {
                throw new UsersProcessorException(
                    'Various agent ids in different functions in one package!',
                    UsersProcessorException::VARIOUS_AGENT_ID
                );
            }
        }
        // Store request in buffer
        $this->addRequestToBuffer('users_' . $agent_id, $request);

        return true;
    }

    /**
     * Check operator id equals to $operator_id for current logged in operator
     *
     * @param int $operator_id Operator id to check
     * @return array Operators info array
     *
     * @throws UsersProcessorException If operators not logged in or if
     *   $operator_id varies from current logged in operator.
     */
    protected function checkOperator($operator_id)
    {
        $operator = $this->getAuthenticationManager()->getOperator();
        if (!$operator) {
            throw new UsersProcessorException(
                "Operator not logged in!",
                UsersProcessorException::ERROR_AGENT_NOT_LOGGED_IN
            );
        }
        if ($operator_id != $operator['operatorid']) {
            throw new UsersProcessorException(
                "Wrong agent id: '{$operator_id}' instead of {$operator['operatorid']}",
                UsersProcessorException::ERROR_WRONG_AGENT_ID
            );
        }

        return $operator;
    }

    /**
     * Mark operator as away. API function
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'agentId': Id of the agent related to users window
     */
    protected function apiAway($args)
    {
        $operator = $this->checkOperator($args['agentId']);
        notify_operator_alive($operator['operatorid'], 1);
        // Update operator's data thus they will be sent with the response.
        $operator['istatus'] = 1;
        $this->getAuthenticationManager()->setOperator($operator);
    }

    /**
     * Mark operator as available. API function
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'agentId': Id of the agent related to users window
     */
    protected function apiAvailable($args)
    {
        $operator = $this->checkOperator($args['agentId']);
        notify_operator_alive($operator['operatorid'], 0);
        // Update operator's data thus they will be sent with the response.
        $operator['istatus'] = 0;
        $this->getAuthenticationManager()->setOperator($operator);
    }

    /**
     * Return updated threads list. API function
     *
     * Triggers
     * {@link \Mibew\EventDispatcher\Events::USERS_UPDATE_THREADS_ALTER} event.
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'agentId': Id of the agent related to users window
     *    - 'revision': last revision number at client side
     * @return array Array of results. It contains the following keys:
     *    - 'threads': array of threads changes
     */
    protected function apiUpdateThreads($args)
    {
        $operator = $this->checkOperator($args['agentId']);

        $since = $args['revision'];
        // Get operator groups
        if (!isset($_SESSION[SESSION_PREFIX . "operatorgroups"])) {
            $_SESSION[SESSION_PREFIX . "operatorgroups"]
                = get_operator_groups_list($operator['operatorid']);
        }
        $group_ids = $_SESSION[SESSION_PREFIX . "operatorgroups"];

        $db = Database::getInstance();
        $query = "SELECT t.*, "
                . " g.vclocalname AS group_localname, "
                . " g.vccommonname AS group_commonname "
            . " FROM {thread} t LEFT OUTER JOIN {opgroup} g ON "
                . " t.groupid = g.groupid "
            . " WHERE t.lrevision > :since "
                . " AND t.istate <> " . Thread::STATE_INVITED
            . ($since == 0
                // Select only active threads at first time when lrevision = 0
                ? " AND t.istate <> " . Thread::STATE_CLOSED
                    . " AND t.istate <> " . Thread::STATE_LEFT
                // Select all threads at when lrevision > 0. It provides the
                // ability to update(and probably hide) closed threads at the
                // clien side.
                : ""
            )
            . (Settings::get('enablegroups') == '1'
                // If groups are enabled select only threads with empty groupid
                // or groups related to current operator
                ? " AND (g.groupid is NULL" . ($group_ids ? " OR g.groupid IN ($group_ids) OR g.groupid IN "
                    . "(SELECT parent FROM {opgroup} "
                    . "WHERE groupid IN ($group_ids)) " : "")
                    . ") "
                : ""
            )
            . " ORDER BY t.threadid";
        $rows = $db->query(
            $query,
            array(':since' => $since),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        $revision = $since;
        $threads = array();
        foreach ($rows as $row) {
            // Create thread instance
            $thread = Thread::createFromDbInfo($row);

            // Calculate agent permissions
            $can_open = !($thread->state == Thread::STATE_CHATTING
                && $thread->agentId != $operator['operatorid']
                && !is_capable(CAN_TAKEOVER, $operator));

            $can_view = ($thread->agentId != $operator['operatorid']
                && $thread->nextAgent != $operator['operatorid']
                && is_capable(CAN_VIEWTHREADS, $operator));

            $can_ban = (Settings::get('enableban') == "1");


            // Get ban info
            $ban = (Settings::get('enableban') == "1")
                ? Ban::loadByAddress($thread->remote)
                : false;
            if ($ban !== false && !$ban->isExpired()) {
                $ban_info = array(
                    'id' => $ban->id,
                    'reason' => $ban->comment,
                );
            } else {
                $ban_info = false;
            }

            // Get user name
            $user_name = get_user_name(
                $thread->userName,
                $thread->remote,
                $thread->userId
            );

            // Get user ip
            $user_ip = preg_replace('/^(\S+)(\s.+)?/', '\\1', $thread->remote);
            $user_ip = filter_var($user_ip, FILTER_VALIDATE_IP);

            // Get thread operartor name
            $next_agent = $thread->nextAgent != 0
                ? operator_by_id($thread->nextAgent)
                : false;
            if ($next_agent) {
                $agent_name = get_operator_name($next_agent);
            } else {
                if ($thread->agentName) {
                    $agent_name = $thread->agentName;
                } else {
                    $group_name = get_group_name(array(
                        'vccommonname' => $row['group_commonname'],
                        'vclocalname' => $row['group_localname'],
                    ));
                    if ($group_name) {
                        $agent_name = '-' . $group_name . '-';
                    } else {
                        $agent_name = '-';
                    }
                }
            }

            // Get first message
            $first_message = null;
            if ($thread->shownMessageId != 0) {
                $line = $db->query(
                    "SELECT tmessage FROM {message} WHERE messageid = ? LIMIT 1",
                    array($thread->shownMessageId),
                    array('return_rows' => Database::RETURN_ONE_ROW)
                );
                if ($line) {
                    $first_message = preg_replace(
                        "/[\r\n\t]+/",
                        " ",
                        $line["tmessage"]
                    );
                }
            }

            $threads[] = array(
                'id' => $thread->id,
                'token' => $thread->lastToken,
                'userId' => $thread->userId,
                'userName' => $user_name,
                'userIp' => $user_ip,
                'remote' => $thread->remote,
                'userAgent' => get_user_agent_version($thread->userAgent),
                'agentId' => $thread->agentId,
                'agentName' => $agent_name,
                'canOpen' => $can_open,
                'canView' => $can_view,
                'canBan' => $can_ban,
                'ban' => $ban_info,
                'state' => $thread->state,
                'totalTime' => $thread->created,
                'waitingTime' => $thread->modified,
                'firstMessage' => $first_message,
            );

            // Get max revision
            if ($thread->lastRevision > $revision) {
                $revision = $thread->lastRevision;
            }

            // Clean up
            unset($thread);
        }

        // Provide an ability to alter threads list
        $arguments = array(
            'threads' => $threads,
        );
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent(Events::USERS_UPDATE_THREADS_ALTER, $arguments);

        // Send results back to the client. "array_values" function should be
        // used to avoid problems with JSON conversion. If there will be gaps in
        // keys (the keys are not serial) JSON Object will be produced instead
        // of an Array.
        return array(
            'threads' => array_values($arguments['threads']),
            'lastRevision' => $revision,
        );
    }

    /**
     * Return updated visitors list. API function.
     *
     * Triggers
     * {@link \Mibew\EventDispatcher\Events::USERS_UPDATE_VISITORS_LOAD} and
     * {@link \Mibew\EventDispatcher\Events::USERS_UPDATE_VISITORS_ALTER}
     * events.
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'agentId': Id of the agent related to users window
     *
     * @return array Array of results. It contains the following keys:
     *  - 'visitors': array of visitors on the site
     */
    protected function apiUpdateVisitors($args)
    {
        // Check access
        $this->checkOperator($args['agentId']);

        // Close old invitations
        invitation_close_old();

        // Remove old visitors
        track_remove_old_visitors();

        // Get instance of event dispatcher
        $dispatcher = EventDispatcher::getInstance();

        // Trigger load event
        $arguments = array(
            'visitors' => false
        );
        $dispatcher->triggerEvent(Events::USERS_UPDATE_VISITORS_LOAD, $arguments);

        // Check if visiors list loaded by plugins
        if (!is_array($arguments['visitors'])) {
            // Load visitors list
            $db = Database::getInstance();
            // Load visitors
            $query = "SELECT v.visitorid, "
                    . "v.userid, "
                    . "v.username, "
                    . "v.firsttime, "
                    . "v.lasttime, "
                    . "v.entry, "
                    . "v.details, "
                    . "t.invitationstate, "
                    . "t.dtmcreated AS invitationtime, "
                    . "t.agentId AS invitedby, "
                    . "v.invitations, "
                    . "v.chats "
                . "FROM {sitevisitor} v "
                    . "LEFT OUTER JOIN {thread} t "
                        . "ON t.threadid = v.threadid "
                . "WHERE v.threadid IS NULL "
                    . "OR (t.istate = :state_invited "
                        . "AND t.invitationstate = :invitation_wait)"
                . "ORDER BY t.invitationstate, v.lasttime DESC, v.invitations";
            $query .= (Settings::get('visitors_limit') == '0')
                ? ""
                : " LIMIT " . Settings::get('visitors_limit');

            $rows = $db->query(
                $query,
                array(
                    ':state_invited' => Thread::STATE_INVITED,
                    ':invitation_wait' => Thread::INVITATION_WAIT,
                ),
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );

            $visitors = array();
            foreach ($rows as $row) {
                // Get visitor details
                $details = track_retrieve_details($row);

                // Get user agent
                $user_agent = get_user_agent_version($details['user_agent']);

                // Get user ip
                $user_ip = preg_replace('/^(\S+)(\s.+)?/', '\\1', $details['remote_host']);
                $user_ip = filter_var($user_ip, FILTER_VALIDATE_IP);

                // Get invitation info
                $row['invited'] = ($row['invitationstate'] == Thread::INVITATION_WAIT);
                if ($row['invited']) {
                    $agent_name = get_operator_name(
                        operator_by_id($row['invitedby'])
                    );
                    $invitation_info = array(
                        'time' => $row['invitationtime'],
                        'agentName' => $agent_name,
                    );
                } else {
                    $invitation_info = false;
                }

                // Create resulting visitor structure
                $visitors[] = array(
                    'id' => (int) $row['visitorid'],
                    'userId' => $row['userid'],
                    'userName' => $row['username'],
                    'userAgent' => $user_agent,
                    'userIp' => $user_ip,
                    'remote' => $details['remote_host'],
                    'firstTime' => $row['firsttime'],
                    'lastTime' => $row['lasttime'],
                    'invitations' => (int) $row['invitations'],
                    'chats' => (int) $row['chats'],
                    'invitationInfo' => $invitation_info,
                );
            }
        } else {
            $visitors = $arguments['visitors'];
        }

        // Provide ability to alter visitors list
        $arguments = array(
            'visitors' => $visitors,
        );
        $dispatcher->triggerEvent(Events::USERS_UPDATE_VISITORS_ALTER, $arguments);

        // Send results back to the client. "array_values" function should be
        // used to avoid problems with JSON conversion. If there will be gaps in
        // keys (the keys are not serial) JSON Object will be produced instead
        // of an Array.
        return array(
            'visitors' => array_values($arguments['visitors']),
        );
    }

    /**
     * Return updated operators list. API function
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'agentId': Id of the agent related to users window
     *
     * @return array Array of results. It contains the following keys:
     *    - 'operators': array of online operators
     */
    protected function apiUpdateOperators($args)
    {
        // Check access and get operators info
        $operator = $this->checkOperator($args['agentId']);

        // Return empty array if show operators option disabled
        if (Settings::get('showonlineoperators') != '1') {
            return array(
                'operators' => array()
            );
        }

        // Check if curent operator is in isolation
        $list_options = in_isolation($operator)
            ? array('isolated_operator_id' => $operator['operatorid'])
            : array();

        // Get operators list
        $operators = get_operators_list($list_options);

        // Create resulting list of operators
        $result_list = array();
        foreach ($operators as $item) {
            if (!operator_is_online($item)) {
                continue;
            }

            $result_list[] = array(
                'id' => (int) $item['operatorid'],
                'name' => htmlspecialchars($item['vclocalename']),
                'away' => (bool) operator_is_away($item)
            );
        }

        // Send operators list to the client side
        return array(
            'operators' => $result_list,
        );
    }

    /**
     * Update chat window state. API function
     * Call periodically by chat window.
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'agentId': Id of the agent related to users window
     */
    protected function apiUpdate($args)
    {
        // Check access and get operator array
        $operator = $this->checkOperator($args['agentId']);

        // Update operator status
        notify_operator_alive($operator['operatorid'], $operator['istatus']);

        // Close old threads
        Thread::closeOldThreads();

        // Load stored requests
        $stored_requests = $this->getRequestsFromBuffer('users_' . $args['agentId']);
        if ($stored_requests !== false) {
            $this->responses = array_merge($this->responses, $stored_requests);
        }
    }

    /**
     * Returns current server time. API function
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'agentId': Id of the agent related to users window
     *
     * @return array Array of results. It contains the following keys:
     *    - 'time': current server time
     */
    protected function apiCurrentTime($args)
    {
        // Check access
        $this->checkOperator($args['agentId']);

        // Return time
        return array(
            'time' => time(),
        );
    }
}
