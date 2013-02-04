<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

/**
 * Incapsulates awaiting users list api related functions.
 *
 * Events triggered by the class (see description of the RequestProcessor class
 * for details):
 *  - usersRequestReceived
 *  - usersReceiveRequestError
 *  - usersCallError
 *  - usersFunctionCall
 *
 * WARNING:
 *  usersResponseReceived registered but never called because of asynchronous
 *  nature of Core-to-Window interaction
 *
 * Implements Singleton pattern
 */
class UsersProcessor extends ClientSideProcessor {

	/**
	 * An instance of the UsersProcessor class
	 * @var UsersProcessor
	 */
	protected static $instance = null;

	/**
	 * Return an instance of the ThreadProcessor class.
	 * @return UsersProcessor
	 */
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class constructor
	 *
	 * Do not use directly __construct method! Use ThreadProcessor::getInstance() instead!
	 * @todo Think about why the method is not protected
	 */
	public function __construct() {
		parent::__construct(array(
			'signature' => '',
			'trusted_signatures' => array(''),
			'event_prefix' => 'users'
		));
	}

	/**
	 * Creates and returns an instance of the MibewAPI class.
	 *
	 * @return MibewAPI
	 */
	protected function getMibewAPIInstance() {
		return MibewAPI::getAPI('MibewAPIUsersInteraction');
	}

	/**
	 * Sends asynchronous request
	 *
	 * @param array $request The 'request' array. See Mibew API for details
	 * @return boolean true on success or false on failure
	 */
	protected function sendAsyncRequest($request) {
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
		$this->addRequestToBuffer('users_'.$agent_id, $request);
		return true;
	}

	/**
	 * Check operator id equals to $operatorId is current logged in operator
	 *
	 * @param int $operatorId Operator id to check
	 * @return array Operators info array
	 *
	 * @throws UsersProcessorException If operators not logged in or if
	 * $operatorId varies from current logged in operator.
	 */
	protected static function checkOperator($operatorId) {
		$operator = get_logged_in();
		if (!$operator) {
			throw new UsersProcessorException(
				"Operator not logged in!",
				UsersProcessorException::ERROR_AGENT_NOT_LOGGED_IN
			);
		}
		if ($operatorId != $operator['operatorid']) {
			throw new UsersProcessorException(
				"Wrong agent id: '{$operatorId}' instead of {$operator['operatorid']}",
				UsersProcessorException::ERROR_WRONG_AGENT_ID
			);
		}
		return $operator;
	}

	/**
	 * Mark operator as away. API function
	 *
	 * @param array $args Associative array of arguments. It must contains
	 * following keys:
	 *  - 'agentId': Id of the agent related to users window
	 */
	protected function apiAway($args) {
		$operator = self::checkOperator($args['agentId']);
		notify_operator_alive($operator['operatorid'], 1);
	}

	/**
	 * Mark operator as available. API function
	 *
	 * @param array $args Associative array of arguments. It must contains
	 * following keys:
	 *  - 'agentId': Id of the agent related to users window
	 */
	protected function apiAvailable($args) {
		$operator = self::checkOperator($args['agentId']);
		notify_operator_alive($operator['operatorid'], 0);
	}

	/**
	 * Return updated threads list. API function
	 *
	 * @global string $session_prefix Session vars prefix
	 * @global int $can_viewthreads View threads permission code
	 * @global int $can_takeover Take threads over permission code
	 * @param array $args Associative array of arguments. It must contains
	 * following keys:
	 *  - 'agentId': Id of the agent related to users window
	 *  - 'revision': last revision number at client side
	 * @return array Array of results. It contains following keys:
	 *  - 'threads': array of threads changes
	 */
	protected function apiUpdateThreads($args) {
		global $session_prefix, $can_viewthreads, $can_takeover;

		$operator = self::checkOperator($args['agentId']);

		$since = $args['revision'];
		// Get operator groups
		if (!isset($_SESSION[$session_prefix."operatorgroups"])) {
			$_SESSION[$session_prefix."operatorgroups"]
				= get_operator_groupslist($operator['operatorid']);
		}
		$groupids = $_SESSION[$session_prefix."operatorgroups"];

		$db = Database::getInstance();
		$query = "select t.*, " .
			" g.vclocalname as group_localname, " .
			" g.vccommonname as group_commonname " .
			" from {chatthread} t left outer join {chatgroup} g on " .
			" t.groupid = g.groupid " .
			" where t.lrevision > :since " .
			($since == 0
				// Select only active threads at first time when lrevision = 0
				? " AND t.istate <> " . Thread::STATE_CLOSED .
					" AND t.istate <> " . Thread::STATE_LEFT
				// Select all threads at when lrevision > 0. It provides the
				// ability to update(and probably hide) closed threads at the
				// clien side.
				: ""
			) .
			(Settings::get('enablegroups') == '1'
				// If groups are enabled select only threads with empty groupid
				// or groups related to current operator
				? " AND (g.groupid is NULL" . ($groupids
					? " OR g.groupid IN ($groupids) OR g.groupid IN " .
						"(SELECT parent FROM {chatgroup} " .
						"WHERE groupid IN ($groupids)) "
					: "") .
				") "
				: ""
			) .
			" ORDER BY t.threadid";
		$rows = $db->query(
			$query,
			array(':since' => $since),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		$revision = $since;
		$threads = array();
		foreach($rows as $row) {
			// Create thread instance
			$thread = Thread::createFromDbInfo($row);

			// Calculate agent permissions
			$can_open = !($thread->state == Thread::STATE_CHATTING
				&& $thread->agentId != $operator['operatorid']
				&& !is_capable($can_takeover, $operator));

			$can_view = ($thread->agentId != $operator['operatorid']
				&& $thread->nextAgent != $operator['operatorid']
				&& is_capable($can_viewthreads, $operator));

			$can_ban = (Settings::get('enableban') == "1");


			// Get ban info
			$ban_info = (Settings::get('enableban') == "1")
				? ban_for_addr($thread->remote)
				: false;
			if ($ban_info !== false) {
				$ban = array(
					'id' => $ban_info['banid'],
					'reason' => $ban_info['comment']
				);
			} else {
				$ban = false;
			}

			// Get user name
			$user_name = get_user_name(
				$thread->userName,
				$thread->remote,
				$thread->userId
			);

			// Get user ip
			if (preg_match("/(\\d+\\.\\d+\\.\\d+\\.\\d+)/", $thread->remote, $matches) != 0) {
				$user_ip = $matches[1];
			} else {
				$user_ip = false;
			}

			// Get thread operartor name
			$nextagent = $thread->nextAgent != 0
				? operator_by_id($thread->nextAgent)
				: false;
			if ($nextagent) {
				$agent_name = get_operator_name($nextagent);
			} else {
				if ($thread->agentName) {
					$agent_name = $thread->agentName;
				} else {
					$group_name = get_group_name(array(
						'vccommonname' => $row['group_commonname'],
						'vclocalname' => $row['group_localname']
					));
					if($group_name) {
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
					"select tmessage from {chatmessage} " .
						" where messageid = ? limit 1",
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
				'userName' => $user_name,
				'userIp' => $user_ip,
				'remote' => $thread->remote,
				'userAgent' => get_useragent_version($thread->userAgent),
				'agentName' => $agent_name,
				'canOpen' => $can_open,
				'canView' => $can_view,
				'canBan' => $can_ban,
				'ban' => $ban,
				'state' => $thread->state,
				'totalTime' => $thread->created,
				'waitingTime' => $thread->modified,
				'firstMessage' => $first_message
			);

			// Get max revision
			if ($thread->lastRevision > $revision) {
				$revision = $thread->lastRevision;
			}

			// Clean up
			unset($thread);
		}

		// Send results back to the client
		return array(
			'threads' => $threads,
			'lastRevision' => $revision
		);
	}

	/**
	 * Return updated visitors list. API function
	 *
	 * @param array $args Associative array of arguments. It must contains
	 * following keys:
	 *  - 'agentId': Id of the agent related to users window
	 * @return array Array of results. It contains following keys:
	 *  - 'visitors': array of visitors on the site
	 */
	protected function apiUpdateVisitors($args) {
		// Check access
		self::checkOperator($args['agentId']);

		// Close old invitations
		invitation_close_old();

		// Remove old visitors and visitors tracks
		track_remove_old_visitors();
		track_remove_old_tracks();

		$db = Database::getInstance();

		// Load visitors
		$query = "SELECT visitorid, userid, username, firsttime, lasttime, " .
			"entry, details, invited, invitationtime, invitedby, " .
			"invitations, chats " .
			"FROM {chatsitevisitor} " .
			"WHERE threadid IS NULL " .
			"ORDER BY invited, lasttime DESC, invitations";
		$query .= (Settings::get('visitors_limit') == '0')
			? ""
			: " LIMIT " . Settings::get('visitors_limit');

		$rows = $db->query(
			$query,
			NULL,
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		$visitors = array();
		foreach ($rows as $row) {

			// Get visitor details
			$details = track_retrieve_details($row);

			// Get user agent
			$user_agent = get_useragent_version($details['user_agent']);

			// Get user ip
			if (preg_match("/(\\d+\\.\\d+\\.\\d+\\.\\d+)/", $details['remote_host'], $matches) != 0) {
				$user_ip = $matches[1];
			} else {
				$user_ip = false;
			}

			// Get invitation info
			if ($row['invited']) {
				$agent_name  = get_operator_name(
					operator_by_id($row['invitedby'])
				);
				$invitation_info = array(
					'time' => $row['invitationtime'],
					'agentName' => $agent_name
				);
			} else {
				$invitation_info = false;
			}

			// Create resulting visitor structure
			$visitors[] = array(
				'id' => (int)$row['visitorid'],
				'userName' => $row['username'],
				'userAgent' => $user_agent,
				'userIp' => $user_ip,
				'remote' => $details['remote_host'],
				'firstTime' => $row['firsttime'],
				'lastTime' => $row['lasttime'],
				'invitations' => (int)$row['invitations'],
				'chats' => (int)$row['chats'],
				'invitationInfo' => $invitation_info
			);
		}

		return array(
			'visitors' => $visitors
		);
	}

	/**
	 * Return updated operators list. API function
	 *
	 * @global string $webim_encoding Encoding for the current locale
	 * @param array $args Associative array of arguments. It must contains
	 * following keys:
	 *  - 'agentId': Id of the agent related to users window
	 * @return array Array of results. It contains following keys:
	 *  - 'operators': array of online operators
	 */
	protected function apiUpdateOperators($args) {
		global $webim_encoding;

		// Check access and get operators info
		$operator = self::checkOperator($args['agentId']);

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
				'id' => (int)$item['operatorid'],
				// Convert name to UTF-8
				'name' => myiconv(
					$webim_encoding,
					"utf-8",
					htmlspecialchars($item['vclocalename'])
				),
				'away' => (bool)operator_is_away($item)
			);
		}

		// Send operators list to the client side
		return array(
			'operators' => $result_list
		);
	}

	/**
	 * Update chat window state. API function
	 *
	 * Call periodically by chat window
	 * @param array $args Associative array of arguments. It must contains
	 * following keys:
	 *  - 'agentId': Id of the agent related to users window
	 */
	protected function apiUpdate($args) {
		// Check access and get operator array
		$operator = self::checkOperator($args['agentId']);

		// Update operator status
		notify_operator_alive($operator['operatorid'], $operator['istatus']);

		// Close old threads
		Thread::closeOldThreads();

		// Load stored requests
		$stored_requests = $this->getRequestsFromBuffer('users_'.$args['agentId']);
		if ($stored_requests !== false) {
			$this->responses = array_merge($this->responses, $stored_requests);
		}
	}

	/**
	 * Returns current server time. API function
	 *
	 * @param array $args Associative array of arguments. It must contains
	 * following keys:
	 *  - 'agentId': Id of the agent related to users window
	 * @return array Array of results. It contains following keys:
	 *  - 'time': current server time
	 */
	protected function apiCurrentTime($args) {
		// Check access
		self::checkOperator($args['agentId']);

		// Return time
		return array(
			'time' => time()
		);
	}
}

/**
 * Class for users processor exceptions
 */
class UsersProcessorException extends RequestProcessorException {
	/**
	 * Operator is not logged in
	 */
	const ERROR_AGENT_NOT_LOGGED_IN = 1;
	/**
	 * Wrong agent id
	 */
	const ERROR_WRONG_AGENT_ID = 2;
	/**
	 * Various agent ids in different functions in one package
	 */
	const VARIOUS_AGENT_ID = 3;
}

?>