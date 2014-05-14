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

namespace Mibew;

// Import namespaces and classes of the core
use Mibew\RequestProcessor\ThreadProcessor;

/**
 * Represents a chat thread
 *
 * Events triggered by the class
 *  - threadChanged
 *
 * Full description of triggered events:
 *
 * 1. "threadChanged" - triggers just after thread saved and only if some thread
 * fields were changed before.
 *
 * An associative array passed to event handler has following keys:
 *  - 'thread': Thread object that was chanded.
 *  - 'changed_fields': list of changed fields. Names of the fields correspond
 *    to class properties (see Thread::propertyMap for details) NOT to fields
 *    names in database.
 *
 * @todo Think about STATE_* and KIND_* constant systems and may be simplifies
 *   them.
 */
class Thread
{
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
     * Visitor was invited to chat by operator
     */
    const STATE_INVITED = 6;

    /**
     * Visitor was not invited to chat
     */
    const INVITATION_NOT_INVITED = 0;
    /**
     * Operator invited visitor and wait for reaction.
     */
    const INVITATION_WAIT = 1;
    /**
     * Invitation was accepted by visitor
     */
    const INVITATION_ACCEPTED = 2;
    /**
     * Invitation was rejected by visitor
     */
    const INVITATION_REJECTED = 3;
    /**
     * Invitation was ignored by visitor. Invitation was automatically closed.
     */
    const INVITATION_IGNORED = 4;

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
     * Message sent by a plugin.
     */
    const KIND_PLUGIN = 7;

    /**
     * Messaging window connection timeout.
     */
    const CONNECTION_TIMEOUT = 30;

    /**
     * Contain mapping of thread object properties to fields in database.
     *
     * Keys are object properties and vlues are {chatthread} table fields.
     * Properties are available via magic __get and __set methods. Real values
     * are stored in the Thread::$threadInfo array.
     *
     * Thread object have following properties:
     *  - 'id': id of the thread
     *  - 'lastRevision': last revision number
     *  - 'state': state of the thread. See Thread::STATE_*
     *  - 'invitationState': state of invitation. See INVITATION_* constants,
     *    defined in libs/invitation.php
     *  - 'lastToken': last chat token
     *  - 'nextAgent': id of the next agent(agent that change current agent in
     *    the chat)
     *  - 'groupId': id of the group related to the thread
     *  - 'shownMessageId': last id of shown message
     *  - 'messageCount': count of user's messages related to the thread
     *  - 'created': unix timestamp of the thread creation
     *  - 'modified': unix timestamp of the thread's last modification
     *  - 'closed': unix timestamp of the moment when the thread was closed
     *  - 'chatStarted': unix timestamp of related to thread chat started
     *  - 'agentId': id of an operator who take part in the chat
     *  - 'agentName': name of an operator who take part in the chat
     *  - 'agentTyping': "1" if operator typing at last ping time and "0"
     *    otherwise
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
        'invitationState' => 'invitationstate',
        'lastToken' => 'ltoken',
        'nextAgent' => 'nextagent',
        'groupId' => 'groupid',
        'shownMessageId' => 'shownmessageid',
        'messageCount' => 'messageCount',
        'created' => 'dtmcreated',
        'modified' => 'dtmmodified',
        'chatStarted' => 'dtmchatstarted',
        'closed' => 'dtmclosed',
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
        'userAgent' => 'userAgent',
    );

    /**
     * Contain loaded from database information about thread
     *
     * Do not use this property manually!
     *
     * @var array
     */
    protected $threadInfo;

    /**
     * List of modified fields.
     *
     * Do not use this property manually!
     *
     * @var array
     */
    protected $changedFields = array();

    /**
     * Create new empty thread in database
     *
     * @return boolean|Thread Returns an object of the Thread class or boolean
     *   false on failure
     */
    public static function create()
    {
        // Get database object
        $db = Database::getInstance();

        // Create new empty thread
        $thread = new self();

        // Create thread
        $db->query("insert into {chatthread} (threadid) values (NULL)");

        // Set thread Id
        // In this case Thread::$threadInfo array use because id of a thread
        // should not be update
        $thread->threadInfo['threadid'] = $db->insertedId();

        // Check if something went wrong
        if (empty($thread->id)) {
            return false;
        }

        // Set initial values
        $thread->lastToken = self::nextToken();
        $thread->created = time();

        return $thread;
    }

    /**
     * Create thread object from database info.
     *
     * @param array $thread_info Associative array of Thread info from database.
     *   It must contains ALL thread table's
     *   FIELDS from the database.
     * @return boolean|Thread Returns an object of the Thread class or boolean
     *   false on failure
     */
    public static function createFromDbInfo($thread_info)
    {
        // Create new empty thread
        $thread = new self();

        // Check thread fields
        $obligatory_fields = array_values($thread->propertyMap);
        foreach ($obligatory_fields as $field) {
            if (!array_key_exists($field, $thread_info)) {
                // Obligatory field is missing
                unset($thread);
                return false;
            }
            // Copy field to Thread object
            $thread->threadInfo[$field] = $thread_info[$field];
        }

        return $thread;
    }

    /**
     * Load thread from database
     *
     * @param int $id ID of the thread to load
     * @return boolean|Thread Returns an object of the Thread class or boolean
     *   false on failure
     */
    public static function load($id, $last_token = null)
    {
        // Check $id
        if (empty($id)) {
            return false;
        }

        // Get database object
        $db = Database::getInstance();

        // Create new empty thread
        $thread = new self();

        // Load thread
        $thread_info = $db->query(
            "SELECT * FROM {chatthread} WHERE threadid = :threadid",
            array(':threadid' => $id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no thread with such id in database
        if (!$thread_info) {
            return;
        }

        // Store thread properties
        $thread->threadInfo = $thread_info;

        // Check if something went wrong
        if ($thread->id != $id) {
            return false;
        }

        // Check last token
        if (!is_null($last_token)) {
            if ($thread->lastToken != $last_token) {
                return false;
            }
        }

        return $thread;
    }

    /**
     * Reopen thread and send message about it
     *
     * @return boolean|Thread Boolean FALSE on failure or thread object on
     *   success
     */
    public static function reopen($id)
    {
        // Load thread
        $thread = self::load($id);

        // Check if user and agent gone
        $user_gone = abs($thread->lastPingUser - time()) > Settings::get('thread_lifetime');
        $agent_gone = abs($thread->lastPingAgent - time()) > Settings::get('thread_lifetime');
        if (Settings::get('thread_lifetime') != 0 && $user_gone && $agent_gone) {
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
        $thread->postMessage(
            self::KIND_EVENTS,
            getstring_("chat.status.user.reopenedthread", $thread->locale, true)
        );

        return $thread;
    }

    /**
     * Close all old threads that were not closed by some reasons
     */
    public static function closeOldThreads()
    {
        if (Settings::get('thread_lifetime') == 0) {
            return;
        }

        $db = Database::getInstance();

        $query = "UPDATE {chatthread} SET "
                . "lrevision = :next_revision, "
                . "dtmmodified = :now, "
                . "dtmclosed = :now, "
                . "istate = :state_closed "
            . "WHERE istate <> :state_closed "
                . "AND istate <> :state_left "
                // Check created timestamp
                . "AND ABS(:now - dtmcreated) > :thread_lifetime "
                // Check pings
                . "AND ( "
                    . "( "
                        // Both user and operator have no connection problems.
                        // Check all pings.
                        . "lastpingagent <> 0 "
                        . "AND lastpinguser <> 0 "
                        . "AND ABS(:now - lastpinguser) > :thread_lifetime "
                        . "AND ABS(:now - lastpingagent) > :thread_lifetime "
                    . ") OR ( "
                        // Only operator have connection problems.
                        // Check user's ping.
                        . "lastpingagent = 0 "
                        . "AND lastpinguser <> 0 "
                        . "AND ABS(:now - lastpinguser) > :thread_lifetime "
                    . ") OR ( "
                        // Only user have connection problems.
                        // Check operator's ping.
                        . "lastpinguser = 0 "
                        . "AND lastpingagent <> 0 "
                        . "AND ABS(:now - lastpingagent) > :thread_lifetime "
                    . ") OR ( "
                        // Both user and operator have connection problems.
                        // Just close thread.
                        . "lastpinguser = 0 "
                        . "AND lastpingagent = 0 "
                    . ") "
                . ")";

        $db->query(
            $query,
            array(
                ':next_revision' => self::nextRevision(),
                ':now' => time(),
                ':state_closed' => self::STATE_CLOSED,
                ':state_left' => self::STATE_LEFT,
                ':thread_lifetime' => Settings::get('thread_lifetime'),
            )
        );
    }

    /**
     * Check if connection limit reached
     *
     * @param string $remote User IP
     * @return boolean TRUE if connection limit reached and FALSE otherwise
     */
    public static function connectionLimitReached($remote)
    {
        if (Settings::get('max_connections_from_one_host') == 0) {
            return false;
        }

        $db = Database::getInstance();
        $result = $db->query(
            "SELECT COUNT(*) AS opened FROM {chatthread} WHERE remote = ? AND istate <> ? AND istate <> ?",
            array(
                $remote,
                Thread::STATE_CLOSED,
                Thread::STATE_LEFT,
            ),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        if ($result && isset($result['opened'])) {
            return $result['opened'] >= Settings::get('max_connections_from_one_host');
        }

        return false;
    }

    /**
     * Implementation of the magic __get method
     *
     * Check if variable with name $name exists in the Thread::$propertyMap
     * array. If it does not exist triggers an error with E_USER_NOTICE level
     * and returns false.
     *
     * @param string $name property name
     * @return mixed
     * @see Thread::$propertyMap
     */
    public function __get($name)
    {
        // Check property existance
        if (!array_key_exists($name, $this->propertyMap)) {
            trigger_error("Undefined property '{$name}'", E_USER_NOTICE);

            return null;
        }

        $field_name = $this->propertyMap[$name];

        return $this->threadInfo[$field_name];
    }

    /**
     * Implementation of the magic __set method
     *
     * Check if variable with name $name exists in the Thread::$propertyMap
     * array before setting. If it does not exist triggers an error
     * with E_USER_NOTICE level and value will NOT set. If previous value is
     * equal to new value the property will NOT be update and NOT update in
     * database when Thread::save method call.
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @return mixed
     * @see Thread::$propertyMap
     */
    public function __set($name, $value)
    {
        if (empty($this->propertyMap[$name])) {
            trigger_error("Undefined property '{$name}'", E_USER_NOTICE);

            return;
        }

        $field_name = $this->propertyMap[$name];

        if (array_key_exists($field_name, $this->threadInfo) && ($this->threadInfo[$field_name] === $value)) {
            return;
        }

        $this->threadInfo[$field_name] = $value;

        if (!in_array($name, $this->changedFields)) {
            $this->changedFields[] = $name;
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
    public function __isset($name)
    {
        if (!array_key_exists($name, $this->propertyMap)) {
            return false;
        }

        $property_name = $this->propertyMap[$name];

        return isset($this->threadInfo[$property_name]);
    }

    /**
     * Remove thread from database
     */
    public function delete()
    {
        $db = Database::getInstance();
        $db->query(
            "DELETE FROM {chatthread} WHERE threadid = :id LIMIT 1",
            array(':id' => $this->id)
        );
    }

    /**
     * Ping the thread.
     *
     * Updates ping time for conversation members and sends messages about
     * connection problems.
     *
     * @param boolean $is_user Indicates user or operator pings thread. Boolean
     *   true for user and boolean false otherwise.
     * @param boolean $is_typing Indicates if user or operator is typing a
     *   message.
     */
    public function ping($is_user, $is_typing)
    {
        // Indicates if revision ID of the thread should be updated on save.
        // Update revision leads to rerender thread in threads list at client
        // side. Do it on every ping is too costly.
        $update_revision = false;
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
                    $message_to_post = getstring_(
                        "chat.status.operator.dead",
                        $this->locale,
                        true
                    );
                    $this->postMessage(
                        self::KIND_CONN,
                        $message_to_post,
                        array('created' => $last_ping_other_side + self::CONNECTION_TIMEOUT)
                    );

                    // And update thread
                    $this->state = self::STATE_WAITING;
                    $this->nextAgent = 0;

                    // Significant fields of the thread (state and nextAgent)
                    // are changed. Update revision ID on save.
                    $update_revision = true;
                }
            } else {
                // _Other_ side is user
                // Update user's last ping time
                $this->lastPingUser = 0;

                // And send a message to operator
                $message_to_post = getstring_(
                    "chat.status.user.dead",
                    $this->locale,
                    true
                );
                $this->postMessage(
                    self::KIND_FOR_AGENT,
                    $message_to_post,
                    array('created' => $last_ping_other_side + self::CONNECTION_TIMEOUT)
                );
            }
        }

        $this->save($update_revision);
    }

    /**
     * Save the thread to the database
     *
     * @param boolean $update_revision Indicates if last modified time and last
     *   revision should be updated.
     */
    public function save($update_revision = true)
    {
        $db = Database::getInstance();

        // Update modified time and last revision if need
        if ($update_revision) {
            $this->lastRevision = $this->nextRevision();
            $this->modified = time();
        }

        // Do not save thread if nothing changed
        if (empty($this->changedFields)) {
            return;
        }

        $values = array();
        $set_clause = array();
        foreach ($this->changedFields as $field_name) {
            $field_db_name = $this->propertyMap[$field_name];
            $set_clause[] = "{$field_db_name} = ?";
            $values[] = $this->threadInfo[$field_db_name];
        }

        $query = "UPDATE {chatthread} t SET " . implode(', ', $set_clause)
            . " WHERE threadid = ?";
        $values[] = $this->id;
        $db->query($query, $values);

        // Trigger thread changed event
        $args = array(
            'thread' => $this,
            'changed_fields' => $this->changedFields,
        );
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent('threadChanged', $args);

        // Clear updated fields
        $this->changedFields = array();
    }

    /**
     * Check if thread is reassigned for another operator
     *
     * Updates thread info, send events messages and avatar message to user.
     *
     * @param array $operator Operator for test
     */
    public function checkForReassign($operator)
    {
        $operator_name = ($this->locale == HOME_LOCALE)
            ? $operator['vclocalename']
            : $operator['vccommonname'];

        $is_operator_correct = $this->nextAgent == $operator['operatorid']
            || $this->agentId == $operator['operatorid'];

        if ($this->state == self::STATE_WAITING && $is_operator_correct) {

            // Prepare message
            if ($this->nextAgent == $operator['operatorid']) {
                $message_to_post = getstring2_(
                    "chat.status.operator.changed",
                    array($operator_name, $this->agentName),
                    $this->locale,
                    true
                );
            } else {
                $message_to_post = getstring2_(
                    "chat.status.operator.returned",
                    array($operator_name),
                    $this->locale,
                    true
                );
            }

            // Update thread info
            $this->state = self::STATE_CHATTING;
            $this->nextAgent = 0;
            $this->agentId = $operator['operatorid'];
            $this->agentName = $operator_name;
            $this->save();

            // Send messages
            $this->postMessage(self::KIND_EVENTS, $message_to_post);
            $this->setupAvatar(
                $operator['vcavatar'] ? $operator['vcavatar'] : ""
            );
        }
    }

    /**
     * Load messages from database corresponding to the thread those ID's more
     * than $lastid
     *
     * @param boolean $is_user Boolean TRUE if messages loads for user
     *   and boolean FALSE if they loads for operator.
     * @param int $lastid ID of the last loaded message.
     * @return array Array of messages. Every message is associative array with
     *   following keys:
     *    - 'id': int, message id;
     *    - 'kind': int, message kind, see Thread::KIND_* for details;
     *    - 'created': int, unix timestamp when message was created;
     *    - 'name': string, name of sender;
     *    - 'message': string, message text;
     *    - 'plugin': string, name of the plugin which sent the message or an
     *      empty string if message was not sent by a plugin.
     *    - 'data' array, arbitrary data attached to the message
     * @see Thread::postMessage()
     */
    public function getMessages($is_user, &$last_id)
    {
        $db = Database::getInstance();

        // Load messages
        $query = "SELECT messageid AS id, ikind AS kind, dtmcreated AS created, "
                . " tname AS name, tmessage AS message, plugin, data "
            . "FROM {chatmessage} "
            . "WHERE threadid = :threadid AND messageid > :lastid "
                . ($is_user ? "AND ikind <> " . self::KIND_FOR_AGENT : "")
            . " ORDER BY messageid";

        $messages = $db->query(
            $query,
            array(
                ':threadid' => $this->id,
                ':lastid' => $last_id,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        foreach ($messages as $key => $msg) {
            // Process data attached to the message
            if (!empty($messages[$key]['data'])) {
                $messages[$key]['data'] = unserialize(
                    $messages[$key]['data']
                );
            } else {
                $messages[$key]['data'] = array();
            }

            // Get last message ID
            if ($msg['id'] > $last_id) {
                $last_id = $msg['id'];
            }
        }

        return $messages;
    }

    /**
     * Send the messsage
     *
     * One can attach arbitrary data to the message by setting 'data' item
     * in the $options array. DO NOT serialize data manually - it will be
     * automatically coverted to array and serialized before save in database
     * and unserialized after retreive form database.
     *
     * One can also set plugin item of the $options array to indicate that
     * message was sent by a plugin.
     *
     * @param int $kind Message kind. One of the Thread::KIND_*
     * @param string $message Message body
     * @param array $options List of additional options. It may contain
     *   following items:
     *    - 'name': string, name of the message sender.
     *    - 'operator_id': int, ID of the operator who sent the message. For
     *      system messages do not set this field.
     *    - 'created': int, unix timestamp of the send time. If you want to set
     *      current time do not set this field.
     *    - 'plugin': string, name of the plugin which sent the message. If
     *      message was not sent by a plugin do not set this field.
     *    - 'data': array with arbitrary data related with message. This value
     *      will be converted to array and serialized before save. If there is
     *      no such data do not set this field.
     *
     * @return int Message ID
     *
     * @see Thread::KIND_USER
     * @see Thread::KIND_AGENT
     * @see Thread::KIND_FOR_AGENT
     * @see Thread::KIND_INFO
     * @see Thread::KIND_CONN
     * @see Thread::KIND_EVENTS
     * @see Thread::KIND_PLUGIN
     * @see Thread::getMessages()
     * @see Thread::postPluginMessage()
     */
    public function postMessage($kind, $message, $options = array())
    {
        $options = is_array($options) ? $options : array();

        // Send message
        return $this->saveMessage($kind, $message, $options);
    }

    /**
     * Close thread and send closing messages to the conversation members
     *
     * @param boolean $is_user Boolean TRUE if user initiate thread closing or
     *   boolean FALSE otherwise
     */
    public function close($is_user)
    {
        // Send message about closing
        if ($is_user) {
            $this->postMessage(
                self::KIND_EVENTS,
                getstring2_(
                    "chat.status.user.left",
                    array($this->userName),
                    $this->locale,
                    true
                )
            );
        } else {
            if ($this->state == self::STATE_INVITED) {
                $this->postMessage(
                    self::KIND_FOR_AGENT,
                    getstring_(
                        "chat.visitor.invitation.canceled",
                        $this->locale,
                        true
                    )
                );
            } else {
                $this->postMessage(
                    self::KIND_EVENTS,
                    getstring2_(
                        "chat.status.operator.left",
                        array($this->agentName),
                        $this->locale,
                        true
                    )
                );
            }
        }

        // Get messages count
        $db = Database::getInstance();

        list($message_count) = $db->query(
            ("SELECT COUNT(*) FROM {chatmessage} "
                . "WHERE {chatmessage}.threadid = :threadid AND ikind = :kind_user"),
            array(
                ':threadid' => $this->id,
                ':kind_user' => Thread::KIND_USER,
            ),
            array(
                'return_rows' => Database::RETURN_ONE_ROW,
                'fetch_type' => Database::FETCH_NUM,
            )
        );

        // Close thread if it's not already closed
        if ($this->state != self::STATE_CLOSED) {
            $this->state = self::STATE_CLOSED;
            $this->closed = time();
            $this->messageCount = $message_count;
            $this->save();
        }
    }

    /**
     * Assign operator to thread
     *
     * @param array $operator Operator who try to take thread
     * @return boolean Boolean TRUE on success or FALSE on failure
     */
    public function take($operator)
    {
        $take_thread = false;
        $message = '';
        $operator_name = ($this->locale == HOME_LOCALE)
            ? $operator['vclocalename']
            : $operator['vccommonname'];

        $no_operator_in_chat = self::STATE_QUEUE
            || self::STATE_WAITING
            || self::STATE_LOADING;

        if ($no_operator_in_chat) {
            // User waiting
            $take_thread = true;
            if ($this->state == self::STATE_WAITING) {
                if ($operator['operatorid'] != $this->agentId) {
                    $message = getstring2_(
                        "chat.status.operator.changed",
                        array($operator_name, $this->agentName),
                        $this->locale,
                        true
                    );
                } else {
                    $message = getstring2_(
                        "chat.status.operator.returned",
                        array($operator_name),
                        $this->locale,
                        true
                    );
                }
            } else {
                $message = getstring2_(
                    "chat.status.operator.joined",
                    array($operator_name),
                    $this->locale,
                    true
                );
            }
        } elseif ($this->state == self::STATE_CHATTING) {
            // User chatting
            if ($operator['operatorid'] != $this->agentId) {
                $take_thread = true;
                $message = getstring2_(
                    "chat.status.operator.changed",
                    array($operator_name, $this->agentName),
                    $this->locale,
                    true
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
            $this->setupAvatar(
                $operator['vcavatar'] ? $operator['vcavatar'] : ""
            );
        }

        return true;
    }

    /**
     * Change user name in the conversation
     *
     * @param string $new_name New user name
     */
    public function renameUser($new_name)
    {
        // Rename only if a new name is realy new
        if ($this->userName != $new_name) {
            // Save old name
            $old_name = $this->userName;
            // Rename user
            $this->userName = $new_name;
            $this->save();

            // Send message about renaming
            $message = getstring2_(
                "chat.status.user.changedname",
                array($old_name, $new_name),
                $this->locale,
                true
            );
            $this->postMessage(self::KIND_EVENTS, $message);
        }
    }

    /**
     * Forbid create instance from outside of the class
     */
    protected function __construct()
    {
    }

    /**
     * Save the messsage in database
     *
     * @param int $kind Message kind. One of the Thread::KIND_*
     * @param string $message Message body
     * @param array $options List of additional options. It may contain
     *   following items:
     *    - 'name': string, name of the message sender.
     *    - 'operator_id': int, ID of the operator who sent the message. For
     *      system messages do not set this field.
     *    - 'created': int, unix timestamp of the send time. If you want to set
     *      current time do not set this field.
     *    - 'plugin': string, name of the plugin which sent the message. If
     *      message was not sent by a plugin do not set this field.
     *    - 'data': array with arbitrary data related with message. This value
     *      will be converted to array and serialized before save. If there is no
     *      such data do not set this field.
     *
     * @return int Message ID
     *
     * @see Thread::KIND_USER
     * @see Thread::KIND_AGENT
     * @see Thread::KIND_FOR_AGENT
     * @see Thread::KIND_INFO
     * @see Thread::KIND_CONN
     * @see Thread::KIND_EVENTS
     * @see Thread::KIND_PLUGIN
     * @see Thread::getMessages()
     */
    protected function saveMessage($kind, $message, $options = array())
    {
        $db = Database::getInstance();

        // TODO: check incoming message (it should be non-empty string)
        // Add default values to options
        $options += array(
            'name' => null,
            'operator_id' => 0,
            'created' => time(),
            'plugin' => '',
            'data' => array(),
        );

        // Prepare message data
        $options['data'] = serialize((array) $options['data']);

        // Prepare query
        $query = "INSERT INTO {chatmessage} ("
                . "threadid, ikind, tmessage, tname, agentId, "
                . "dtmcreated, plugin, data"
            . ") VALUES ("
                . ":threadid, :kind, :message, :name, :agentid, "
                . ":created, :plugin, :data"
            . ")";

        $values = array(
            ':threadid' => $this->id,
            ':kind' => $kind,
            ':message' => $message,
            ':name' => $options['name'],
            ':agentid' => $options['operator_id'],
            ':created' => $options['created'],
            ':plugin' => $options['plugin'],
            ':data' => $options['data'],
        );

        // Execute query
        $db->query($query, $values);

        return $db->insertedId();
    }

    /**
     * Return next revision number (last revision number plus one)
     *
     * @return int revision number
     */
    protected static function nextRevision()
    {
        $db = Database::getInstance();
        $db->query("UPDATE {chatrevision} SET id=LAST_INSERT_ID(id+1)");
        $val = $db->insertedId();

        return $val;
    }

    /**
     * Create thread token
     *
     * @return int Thread token
     */
    protected static function nextToken()
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $token_arr = unpack('N', "\x0" . openssl_random_pseudo_bytes(3));
            $token = $token_arr[1];
        } else {
            $token = mt_rand(99999, 99999999);
        }

        return $token;
    }

    /**
     * Set operator avatar in the user's chat window
     *
     * @param string $link URL of the new operator avatar
     */
    protected function setupAvatar($link)
    {
        $processor = ThreadProcessor::getInstance();
        $processor->call(
            array(
                array(
                    'function' => 'setupAvatar',
                    'arguments' => array(
                        'threadId' => $this->id,
                        'token' => $this->lastToken,
                        'return' => array(),
                        'references' => array(),
                        'recipient' => 'user',
                        'imageLink' => $link,
                    ),
                ),
            ),
            true
        );
    }
}
