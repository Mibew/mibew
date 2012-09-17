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
 * Represents a chat thread
 *
 * @todo Think about STATE_* and KIND_* constant systems and may be simplifies them.
 */
Class Thread {

	/**
	 * User in the users queue
	 */
	const STATE_QUEUE = 0;
	/**
	 * User waiting for operator
	 */
	const STATE_WAITING = 1;
	/**
	 * Conversation in progress
	 */
	const STATE_CHATTING = 2;
	/**
	 * Thread closed
	 */
	const STATE_CLOSED = 3;
	/**
	 * Thread just created
	 */
	const STATE_LOADING = 4;
	/**
	 * User left message without starting a conversation
	 */
	const STATE_LEFT = 5;

	/**
	 * Message sent by user
	 */
	const KIND_USER = 1;
	/**
	 * Message sent by operator
	 */
	const KIND_AGENT = 2;
	/**
	 * Hidden system message to operator
	 */
	const KIND_FOR_AGENT = 3;
	/**
	 * System messages for user and operator
	 */
	const KIND_INFO = 4;
	/**
	 * Message for user if operator have connection problems
	 */
	const KIND_CONN = 5;
	/**
	 * System message about some events (like rename).
	 */
	const KIND_EVENTS = 6;
	/**
	 * Message with operators avatar
	 */
	const KIND_AVATAR = 7;

	/**
	 * Messaging window connection timeout.
	 */
	const CONNECTION_TIMEOUT = 30;

	/**
	 * Contain mapping of thread object properties to fields in database.
	 *
	 * Keys are object properties and vlues are {chatthread} table fields. Properties are available via magic __get
	 * and __set methods. Real values are stored in the Thread::$threadInfo array.
	 *
	 * Thread object have following properties:
	 *  - 'id': id of the thread
	 *  - 'lastRevision': last revision number
	 *  - 'state': state of the thread. See Thread::STATE_*
	 *  - 'lastToken': last chat token
	 *  - 'nextAgent': id of the next agent(agent that change current agent in the chat)
	 *  - 'groupId': id of the group related to the thread
	 *  - 'shownMessageId': last id of shown message
	 *  - 'messageCount': count of user's messages related to the thread
	 *  - 'created': unix timestamp of the thread creation
	 *  - 'modified': unix timestamp of the thread's last modification
	 *  - 'chatStarted': unix timestamp of related to thread chat started
	 *  - 'agentId': id of an operator who take part in the chat
	 *  - 'agentName': name of an operator who take part in the chat
	 *  - 'agentTyping': "1" if operator typing at last ping time and "0" otherwise
	 *  - 'lastPingAgent': unix timestamp of last operator ping
	 *  - 'locale': locale code of the chat related to thread
	 *  - 'userId': id of an user who take part in the chat
	 *  - 'userName': name of an user who take part in the chat
	 *  - 'userTyping': "1" if user typing at last ping time and "0" otherwise
	 *  - 'lastPingUser': unix timestamp of last user ping
	 *  - 'remote': user's IP
	 *  - 'referer': content of HTTP Referer header for user
	 *  - 'userAgent': content of HTTP User-agent header for user
	 *
	 * @var array
	 *
	 * @see Thread::__get()
	 * @see Thread::__set()
	 * @see Thread::$threadInfo
	 */
	protected $propertyMap = array(
		'id' => 'threadid',

		'lastRevision' => 'lrevision',
		'state' => 'istate',
		'lastToken' => 'ltoken',

		'nextAgent' => 'nextagent',
		'groupId' => 'groupid',

		'shownMessageId' => 'shownmessageid',
		'messageCount' => 'messageCount',

		'created' => 'dtmcreated',
		'modified' => 'dtmmodified',
		'chatStarted' => 'dtmchatstarted',

		'agentId' => 'agentId',
		'agentName' => 'agentName',
		'agentTyping' => 'agentTyping',
		'lastPingAgent' => 'lastpingagent',

		'locale' => 'locale',

		'userId' => 'userid',
		'userName' => 'userName',
		'userTyping' => 'userTyping',
		'lastPingUser' => 'lastpinguser',

		'remote' => 'remote',
		'referer' => 'referer',
		'userAgent' => 'userAgent'
	);

	/**
	 * Contain loaded from database information about thread
	 *
	 * Do not use this property manually!
	 * @var array
	 */
	protected $threadInfo;

	/**
	 * List of modified fields.
	 *
	 * Do not use this property manually!
	 * @var array
	 */
	protected $updatedFields = array();

	/**
	 * Load thread from database or create a new one in the database if $id is empty.
	 *
	 * @param int $id ID of the thread to load
	 */
	protected function __construct($id = null) {
		// Get database object
		$db = Database::getInstance();

		if (! empty($id)) {
			// Load thread
			$thread_info = $db->query(
				"select * from {chatthread} where threadid = :threadid",
				array(
					':threadid' => $id
				),
				array('return_rows' => Database::RETURN_ONE_ROW)
			);

			if (! $thread_info) {
				return;
			}

			$this->threadInfo = $thread_info;
		} else {
			// Create thread
			$db->query("insert into {chatthread} (threadid) values (NULL)");
			// Set initial values
			// In this case Thread::$threadInfo array use because id of a thread should not be update
			$this->threadInfo['threadid'] = $db->insertedId();
		}
	}

	/**
	 * Create new empty thread in database
	 *
	 * @return boolean|Thread Returns an object of the Thread class or boolean false on failure
	 */
	public static function create() {
		// Create new empty thread
		$thread = new self();
		// Check if something went wrong
		if (empty($thread->id)) {
			return false;
		}
		return $thread;
	}

	/**
	 * Load thread from database
	 *
	 * @param int $id ID of the thread to load
	 * @return boolean|Thread Returns an object of the Thread class or boolean false on failure
	 */
	public static function load($id) {
		// Check $id
		if (empty($id)) {
			return false;
		}

		// Load thread
		$thread = new self($id);

		// Check if something went wrong
		if ($thread->id != $id) {
			return false;
		}
		return $thread;
	}

	/**
	 * Reopen thread and send message about it
	 *
	 * @return boolean|Thread Boolean FALSE on failure or thread object on success
	 */
	public static function reopen($id) {
		// Load thread
		$thread = self::load($id);
		// Check if user and agent gone
		if (Settings::get('thread_lifetime') != 0 &&
				abs($thread->lastPingUser - time()) > Settings::get('thread_lifetime') &&
				abs($thread->lastPingAgent - time()) > Settings::get('thread_lifetime')) {
			unset($thread);
			return false;
		}

		// Check if thread closed
		if ($thread->state == self::STATE_CLOSED || $thread->state == self::STATE_LEFT) {
			unset($thread);
			return false;
		}

		// Reopen thread
		if ($thread->state == self::STATE_WAITING) {
			$thread->nextAgent = 0;
			$thread->save();
		}

		// Send message
		$thread->postMessage(self::KIND_EVENTS, getstring_("chat.status.user.reopenedthread", $thread->locale));
		return $thread;
	}

	/**
	 * Close all old threads that were not closed by some reasons
	 */
	public static function closeOldThreads() {
		if (Settings::get('thread_lifetime') == 0) {
			return;
		}

		$db = Database::getInstance();

		$query = "update {chatthread} set lrevision = :next_revision, " .
			"dtmmodified = :now, istate = :state_closed " .
			"where istate <> :state_closed and istate <> :state_left " .
			"and ((lastpingagent <> 0 and lastpinguser <> 0 and " .
			"(ABS(:now - lastpinguser) > :thread_lifetime and " .
			"ABS(:now - lastpingagent) > :thread_lifetime)) or " .
			"(lastpingagent = 0 and lastpinguser <> 0 and " .
			"ABS(:now - lastpinguser) > :thread_lifetime))";

		$db->query(
			$query,
			array(
				':next_revision' => self::nextRevision(),
				':now' => time(),
				':state_closed' => self::STATE_CLOSED,
				':state_left' => self::STATE_LEFT,
				':thread_lifetime' => Settings::get('thread_lifetime')
			)
		);
	}

	/**
	 * Return next revision number (last revision number plus one)
	 *
	 * @return int revision number
	 */
	protected static function nextRevision() {
		$db = Database::getInstance();
		$db->query("update {chatrevision} set id=LAST_INSERT_ID(id+1)");
		$val = $db->insertedId();
		return $val;
	}

	/**
	 * Implementation of the magic __get method
	 *
	 * Check if variable with name $name exists in the Thread::$propertyMap array.
	 * If it does not exist triggers an error with E_USER_NOTICE level and returns false.
	 *
	 * @param string $name property name
	 * @return mixed
	 * @see Thread::$propertyMap
	 */
	public function __get($name) {
		// Check property existance
		if (! array_key_exists($name, $this->propertyMap)) {
			trigger_error("Undefined property '{$name}'", E_USER_NOTICE);
			return NULL;
		}

		$field_name = $this->propertyMap[$name];
		return $this->threadInfo[$field_name];
	}

	/**
	 * Implementation of the magic __set method
	 *
	 * Check if variable with name $name exists in the Thread::$propertyMap array before setting.
	 * If it does not exist triggers an error with E_USER_NOTICE level and value will NOT set.
	 *
	 * @param string $name Property name
	 * @param mixed $value Property value
	 * @return mixed
	 * @see Thread::$propertyMap
	 */
	public function __set($name, $value) {
		if (empty($this->propertyMap[$name])) {
			trigger_error("Undefined property '{$name}'", E_USER_NOTICE);
			return;
		}

		$field_name = $this->propertyMap[$name];
		$this->threadInfo[$field_name] = $value;

		if (! in_array($field_name, $this->updatedFields)) {
			$this->updatedFields[] = $field_name;
		}
	}

	/**
	 * Implementation of the magic __isset method
	 *
	 * Check if variable with $name exists.
	 *
	 * param string $name Variable name
	 * return boolean True if variable exists and false otherwise
	 */
	public function __isset($name) {
		if (!array_key_exists($name, $this->propertyMap)) {
			return false;
		}

		$property_name = $this->propertyMap[$name];
		return isset($this->threadInfo[$property_name]);
	}

	/**
	 * Remove thread from database
	 */
	public function delete() {
		$db = Database::getInstance();
		$db->query(
			"DELETE FROM {chatthread} WHERE threadid = :id LIMIT 1",
			array(':id' => $this->id)
		);
	}

	/**
	 * Ping the thread.
	 *
	 * Updates ping time for conversation members and sends messages about connection problems.
	 *
	 * @param boolean $is_user Indicates user or operator pings thread. Boolean true for user and boolean false
	 * otherwise.
	 * @param boolean $is_typing Indicates if user or operator is typing a message.
	 */
	public function ping($is_user, $is_typing) {
		// Last ping time of other side
		$last_ping_other_side = 0;
		// Update last ping time
		if ($is_user) {
			$last_ping_other_side = $this->lastPingAgent;
			$this->lastPingUser = time();
			$this->userTyping = $is_typing ? "1" : "0";
		} else {
			$last_ping_other_side = $this->lastPingUser;
			$this->lastPingAgent = time();
			$this->agentTyping = $is_typing ? "1" : "0";
		}

		// Update thread state for the first user ping
		if ($this->state == self::STATE_LOADING && $is_user) {
			$this->state = self::STATE_QUEUE;
			$this->save();
			return;
		}

		// Check if other side of the conversation have connection problems
		if ($last_ping_other_side > 0 && abs(time() - $last_ping_other_side) > self::CONNECTION_TIMEOUT) {
			// Connection problems detected
			if ($is_user) {
				// _Other_ side is operator
				// Update operator's last ping time
				$this->lastPingAgent = 0;

				// Check if user chatting at the moment
				if ($this->state == self::STATE_CHATTING) {
					// Send message to user
					$message_to_post = getstring_("chat.status.operator.dead", $this->locale);
					$this->postMessage(
						self::KIND_CONN,
						$message_to_post,
						null,
						null,
						$last_ping_other_side + self::CONNECTION_TIMEOUT
					);

					// And update thread
					$this->state = self::STATE_WAITING;
					$this->nextAgent = 0;
				}
			} else {
				// _Other_ side is user
				// Update user's last ping time
				$this->lastPingUser = 0;

				// And send a message to operator
				$message_to_post = getstring_("chat.status.user.dead", $this->locale);
				$this->postMessage(
					self::KIND_FOR_AGENT,
					$message_to_post,
					null,
					null,
					$last_ping_other_side + self::CONNECTION_TIMEOUT
				);
			}
		}

		$this->save();
	}

	/**
	 * Save the thread to the database
	 */
	public function save(){
		$db = Database::getInstance();

		$query = "update {chatthread} t " .
			"set lrevision = ?, dtmmodified = ?";

		$values = array();
		$values[] = $this->nextRevision();
		$values[] = time();

		foreach ($this->updatedFields as $field_name) {
			$query .= ", {$field_name} = ?" ;
			$values[] = $this->threadInfo[$field_name];
		}

		$query .= " where threadid = ?";
		$values[] = $this->id;
		$db->query($query, $values);
	}

	/**
	 * Check if thread is reassigned for another operator
	 *
	 * Updates thread info, send events messages and avatar message to user
	 * @global string $home_locale
	 * @param array $operator Operator for test
	 */
	public function checkForReassign($operator) {
		global $home_locale;

		$operator_name = ($this->locale == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];

		if ($this->state == self::STATE_WAITING &&
			($this->nextAgent == $operator['operatorid'] || $this->agentId == $operator['operatorid'])) {

			// Prepare message
			if ($this->nextAgent == $operator['operatorid']) {
				$message_to_post = getstring2_(
					"chat.status.operator.changed",
					array($operator_name, $this->agentName),
					$this->locale
				);
			} else {
				$message_to_post = getstring2_("chat.status.operator.returned", array($operator_name), $this->locale);
			}

			// Update thread info
			$this->state = self::STATE_CHATTING;
			$this->nextAgent = 0;
			$this->agentId = $operator['operatorid'];
			$this->agentName = $operator_name;
			$this->save();

			// Send messages
			$this->postMessage(self::KIND_EVENTS, $message_to_post);
			$this->postMessage(self::KIND_AVATAR, $operator['vcavatar'] ? $operator['vcavatar'] : "");
		}
	}

	/**
	 * Load messages from database corresponding to the thread those ID's more than $lastid
	 *
	 * @param boolean $is_user Boolean TRUE if messages loads for user and boolean FALSE if they loads for operator.
	 * @param int $lastid ID of the last loaded message.
	 * @return array Array of messages
	 * @see Thread::postMessage()
	 */
	public function getMessages($is_user, &$last_id) {

		$db = Database::getInstance();

		// Load messages
		$messages = $db->query(
			"select messageid,ikind,dtmcreated as created,tname,tmessage from {chatmessage} " .
			"where threadid = :threadid and messageid > :lastid " .
			($is_user ? "and ikind <> " . self::KIND_FOR_AGENT : "") .
			" order by messageid",
			array(
				':threadid' => $this->id,
				':lastid' => $last_id
			),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		foreach ($messages as $msg) {
			// Get last message ID
			if ($msg['messageid'] > $last_id) {
				$last_id = $msg['messageid'];
			}
		}

		return $messages;
	}

	/**
	 * Send the messsage
	 *
	 * @param int $kind Message kind. One of the Thread::KIND_*
	 * @param string $message Message body
	 * @param string|null $from Sender name
	 * @param int|null $opid operator id. Use NULL for system messages
	 * @param int|null $time unix timestamp of the send time. Use NULL for current time.
	 * @return int Message ID
	 *
	 * @see Thread::KIND_USER
	 * @see Thread::KIND_AGENT
	 * @see Thread::KIND_FOR_AGENT
	 * @see Thread::KIND_INFO
	 * @see Thread::KIND_CONN
	 * @see Thread::KIND_EVENTS
	 * @see Thread::KIND_AVATAR
	 * @see Thread::getMessages()
	 */
	public function postMessage($kind, $message, $from = null, $opid = null, $time = null) {
		$db = Database::getInstance();

		$query = "INSERT INTO {chatmessage} " .
			"(threadid,ikind,tmessage,tname,agentId,dtmcreated) " .
			"VALUES (:threadid,:kind,:message,:name,:agentid,:created)";

		$values = array(
			':threadid' => $this->id,
			':kind' => $kind,
			':message' => $message,
			':name' => ($from ? $from : "null"),
			':agentid' => ($opid ? $opid : 0),
			':created' => ($time ? $time : time())
		);

		$db->query($query, $values);
		return $db->insertedId();
	}

	/**
	 * Close thread and send closing messages to the conversation members
	 *
	 * @param boolean $is_user Boolean TRUE if user initiate thread closing or boolean FALSE otherwise
	 */
	public function close($is_user) {
		$db = Database::getInstance();

		// Get messages count
		list($message_count) = $db->query(
			"SELECT COUNT(*) FROM {chatmessage} WHERE {chatmessage}.threadid = :threadid AND ikind = :kind_user",
			array(
				':threadid' => $this->id,
				':kind_user' => Thread::KIND_USER
			),
			array(
				'return_rows' => Database::RETURN_ONE_ROW,
				'fetch_type' => Database::FETCH_NUM
			)
		);

		// Close thread if it's not already closed
		if ($this->state != self::STATE_CLOSED) {
			$this->state = self::STATE_CLOSED;
			$this->messageCount = $message_count;
			$this->save();
		}

		// Send message about closing
		$message = '';
		if ($is_user) {
			$message = getstring2_("chat.status.user.left", array($this->userName), $this->locale);
		} else {
			$message = getstring2_("chat.status.operator.left", array($this->agentName), $this->locale);
		}
		$this->postMessage(self::KIND_EVENTS, $message);
	}

	/**
	 * Assign operator to thread
	 *
	 * @global string $home_locale
	 * @param array $operator Operator who try to take thread
	 * @return boolean Boolean TRUE on success or FALSE on failure
	 */
	public function take($operator) {
		global $home_locale;

		$take_thread = false;
		$message = '';
		$operator_name = ($this->locale == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];

		if ($this->state == self::STATE_QUEUE || $this->state == self::STATE_WAITING || $this->state == self::STATE_LOADING) {
			// User waiting
			$take_thread = true;
			if ($this->state == self::STATE_WAITING) {
				if ($operator['operatorid'] != $this->agentId) {
					$message = getstring2_(
						"chat.status.operator.changed",
						array($operator_name, $this->agentName),
						$this->locale
					);
				} else {
					$message = getstring2_("chat.status.operator.returned", array($operator_name), $this->locale);
				}
			} else {
				$message = getstring2_("chat.status.operator.joined", array($operator_name), $this->locale);
			}
		} elseif ($this->state == self::STATE_CHATTING) {
			// User chatting
			if ($operator['operatorid'] != $this->agentId) {
				$take_thread = true;
				$message = getstring2_(
					"chat.status.operator.changed",
					array($operator_name, $this->agentName),
					$this->locale
				);
			}
		} else {
			// Thread closed
			return false;
		}

		// Change operator and update chat info
		if ($take_thread) {
			$this->state = self::STATE_CHATTING;
			$this->nextAgent = 0;
			$this->agentId = $operator['operatorid'];
			$this->agentName = $operator_name;
			if (empty($this->chatStarted)) {
				$this->chatStarted = time();
			}
			$this->save();
		}

		// Send message
		if ($message) {
			$this->postMessage(self::KIND_EVENTS, $message);
			$this->postMessage(self::KIND_AVATAR, $operator['vcavatar'] ? $operator['vcavatar'] : "");
		}
		return true;
	}

	/**
	 * Change user name in the conversation
	 *
	 * @param string $new_name New user name
	 */
	public function renameUser($new_name) {
		// Rename only if a new name is realy new
		if ($this->userName != $new_name) {
			// Rename user
			$this->userName = $new_name;
			$this->save();

			// Send message about renaming
			$message = getstring2_(
				"chat.status.user.changedname",
				array($this->userName, $new_name),
				$this->locale
			);
			$this->postMessage(self::KIND_EVENTS, $message);
		}
	}
}

?>