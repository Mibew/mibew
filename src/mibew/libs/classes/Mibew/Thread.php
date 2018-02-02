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

namespace Mibew;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;

/**
 * Represents a chat thread
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
     * ID of the thread.
     * @var int|bool
     */
    public $id;

    /**
     * Number of the last revision
     * @var int
     */
    public $lastRevision;

    /**
     * State of the thread. See Thread::STATE_* constants for details.
     * @var int
     */
    public $state;

    /**
     * State of the invitation. See Thread::INVITATION_* constants for details.
     * @var int
     */
    public $invitationState;

    /**
     * The last token of the chat thread.
     * @var string
     */
    public $lastToken;

    /**
     * ID of the next agent(agent that changes the current agent in the chat).
     * @var int
     */
    public $nextAgent;

    /**
     * ID of the group related with the thread.
     *
     * If there is no attached group the value should be equal to 0 (zero).
     *
     * @var int
     */
    public $groupId;

    /**
     * ID of the last shown message.
     * @var int
     */
    public $shownMessageId;

    /**
     * Count of user's messages related to the thread.
     * @var int
     */
    public $messageCount;

    /**
     * Unix timestamp of the moment the thread was created.
     * @var int
     */
    public $created;

    /**
     * Unix timestamp of the moment the thread was modified last time.
     * @var int
     */
    public $modified;

    /**
     * Unix timestamp of the moment when the thread was closed.
     * @var int
     */
    public $closed;

    /**
     * Unix timestamp of the moment the chat related to the thread was started.
     * @var int
     */
    public $chatStarted;

    /**
     * ID of an operator who take part in the chat.
     * @var int
     */
    public $agentId;

    /**
     * Name of an operator who take part in the chat.
     * @var string
     */
    public $agentName;

    /**
     * Indicates if the opertor who take part in the chat is typing or not.
     *
     * It is equal to "1" if the operator was typing at the last ping time and
     * "0" otherwise.
     * @var int
     */
    public $agentTyping;

    /**
     * Unix timestamp of the moment the operator was pinged for the last time.
     * @var int
     */
    public $lastPingAgent;

    /**
     * Locale code of the chat thread.
     * @var string
     */
    public $locale;

    /**
     * ID of a user who take part in the chat.
     * @var string
     */
    public $userId;

    /**
     * Name of a user who take part in the chat.
     * @var string
     */
    public $userName;

    /**
     * Indicates if the user who take part in the chat is typing or not.
     *
     * It is equal to "1" if the user was typing at the last ping time and "0"
     * otherwise
     * @var int
     */
    public $userTyping;

    /**
     * Unix timestamp of the moment the user was pinged for the last time.
     * @var int
     */
    public $lastPingUser;

    /**
     * User's IP.
     * @var string
     */
    public $remote;

    /**
     * Content of HTTP "Referer" header for the user.
     * @var string
     */
    public $referer;

    /**
     * Content of HTTP "User-agent" header for the user.
     * @var string
     */
    public $userAgent;

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
        $thread->populateFromDbFields($thread_info);

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

        // Load thread
        $thread_info = Database::getInstance()->query(
            "SELECT * FROM {thread} WHERE threadid = :threadid",
            array(':threadid' => $id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no thread with such id in database
        if (!$thread_info) {
            return;
        }

        // Create new empty thread and populate it with the values from database
        $thread = new self();
        $thread->populateFromDbFields($thread_info);

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
            getlocal('Visitor joined chat again', null, $thread->locale, true)
        );

        return $thread;
    }

    /**
     * Close all old threads that were not closed by some reasons.
     */
    public static function closeOldThreads()
    {
        if (Settings::get('thread_lifetime') == 0) {
            // Threads live forever.
            return;
        }

        // We need to run only one instance of cleaning process.
        $lock = new ProcessLock('threads_close_old');

        if ($lock->get()) {
            $query = "SELECT * FROM {thread} "
                . "WHERE istate <> :state_closed "
                    . "AND istate <> :state_left "
                    // Check created timestamp
                    . "AND ABS(:now - dtmcreated) > :thread_lifetime "
                    // Check pings
                    . "AND ( "
                        . "( "
                            // Both user and operator have no connection
                            // problems. Check all pings.
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

            // Get appropriate threads
            $now = time();
            $rows = Database::getInstance()->query(
                $query,
                array(
                    ':now' => $now,
                    ':state_closed' => self::STATE_CLOSED,
                    ':state_left' => self::STATE_LEFT,
                    ':thread_lifetime' => Settings::get('thread_lifetime'),
                ),
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );

            // Perform the cleaning
            if (count($rows) > 0) {
                $revision = self::nextRevision();
                foreach ($rows as $row) {
                    $thread = Thread::createFromDbInfo($row);
                    $thread->lastRevision = $revision;
                    $thread->modified = $now;
                    $thread->closed = $now;
                    $thread->state = self::STATE_CLOSED;
                    $thread->save(false);
                    unset($thread);
                }
            }

            // Release the lock
            $lock->release();
        }
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

        $result = Database::getInstance()->query(
            "SELECT COUNT(*) AS opened FROM {thread} WHERE remote = ? AND istate <> ? AND istate <> ?",
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
     * Class constructor.
     */
    public function __construct()
    {
        // Set the defaults
        $this->id = false;
        $this->userName = '';
        $this->agentId = 0;
        $this->created = time();
        $this->modified = time();
        $this->chatStarted = 0;
        $this->closed = 0;
        $this->lastRevision = 0;
        $this->state = self::STATE_QUEUE;
        $this->invitationState = self::INVITATION_NOT_INVITED;
        $this->lastToken = self::nextToken();
        $this->nextAgent = 0;
        $this->lastPingAgent = 0;
        $this->lastPingUser = 0;
        $this->userTyping = 0;
        $this->agentTyping = 0;
        $this->shownMessageId = 0;
        $this->messageCount = 0;
        $this->groupId = 0;
    }

    /**
     * Remove thread from database
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::THREAD_DELETE} event.
     */
    public function delete()
    {
        Database::getInstance()->query(
            "DELETE FROM {thread} WHERE threadid = :id LIMIT 1",
            array(':id' => $this->id)
        );

        $args = array('id' => $this->id);
        EventDispatcher::getInstance()->triggerEvent(Events::THREAD_DELETE, $args);
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
        if ($last_ping_other_side > 0 && abs(time() - $last_ping_other_side) > Settings::get('connection_timeout')) {
            // Connection problems detected
            if ($is_user) {
                // _Other_ side is operator
                // Update operator's last ping time
                $this->lastPingAgent = 0;

                // Check if user chatting at the moment
                if ($this->state == self::STATE_CHATTING) {
                    // Send message to user
                    $message_to_post = getlocal(
                        'Your operator has connection issues. We have moved you to a priorty position in the queue. Sorry for keeping you waiting.',
                        null,
                        $this->locale,
                        true
                    );
                    $this->postMessage(
                        self::KIND_CONN,
                        $message_to_post,
                        array('created' => $last_ping_other_side + Settings::get('connection_timeout'))
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

                // And send a message to operator.
                if ($this->state == self::STATE_CHATTING) {
                    $message_to_post = getlocal(
                        'Visitor closed chat window',
                        null,
                        $this->locale,
                        true
                    );
                    $this->postMessage(
                        self::KIND_FOR_AGENT,
                        $message_to_post,
                        array('created' => $last_ping_other_side + Settings::get('connection_timeout'))
                    );
                }
            }
        }

        $this->save($update_revision);
    }

    /**
     * Save the thread to the database
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::THREAD_UPDATE} and
     * {@link \Mibew\EventDispatcher\Events::THREAD_CREATE} events.
     *
     * @param boolean $update_revision Indicates if last modified time and last
     *   revision should be updated.
     */
    public function save($update_revision = true)
    {
        // Update modification time and revision number only if needed
        if ($update_revision) {
            $this->lastRevision = $this->nextRevision();
            $this->modified = time();
        }

        $db = Database::getInstance();
        if (!$this->id) {
            $db->query(
                ('INSERT INTO {thread} ('
                    . 'username, userid, agentname, agentid, '
                    . 'dtmcreated, dtmchatstarted, dtmmodified, dtmclosed, '
                    . 'lrevision, istate, invitationstate, ltoken, remote, '
                    . 'referer, nextagent, locale, lastpinguser, '
                    . 'lastpingagent, usertyping, agenttyping, '
                    . 'shownmessageid, useragent, messagecount, groupid'
                . ') VALUES ('
                    . ':user_name, :user_id, :agent_name, :agent_id, '
                    . ':created, :chat_started, :modified, :closed, '
                    . ':revision, :state, :invitation_state, :token, :remote, '
                    . ':referer, :next_agent, :locale, :last_ping_user, '
                    . ':last_ping_agent, :user_typing, :agent_typing, '
                    . ':shown_message_id, :user_agent, :message_count, :group_id '
                . ')'),
                array(
                    ':user_name' => $this->userName,
                    ':user_id' => $this->userId,
                    ':agent_name' => $this->agentName,
                    ':agent_id' => $this->agentId,
                    ':created' => $this->created,
                    ':chat_started' => $this->chatStarted,
                    ':modified' => $this->modified,
                    ':closed' => $this->closed,
                    ':revision' => $this->lastRevision,
                    ':state' => $this->state,
                    ':invitation_state' => $this->invitationState,
                    ':token' => $this->lastToken,
                    ':remote' => $this->remote,
                    ':referer' => $this->referer,
                    ':next_agent' => $this->nextAgent,
                    ':locale' => $this->locale,
                    ':last_ping_user' => $this->lastPingUser,
                    ':last_ping_agent' => $this->lastPingAgent,
                    ':user_typing' => $this->userTyping,
                    ':agent_typing' => $this->agentTyping,
                    ':shown_message_id' => $this->shownMessageId,
                    ':user_agent' => $this->userAgent,
                    ':message_count' => $this->messageCount,
                    ':group_id' => $this->groupId,
                )
            );
            $this->id = $db->insertedId();

            $args = array('thread' => $this);
            EventDispatcher::getInstance()->triggerEvent(Events::THREAD_CREATE, $args);
        } else {
            // Get the original state of the thread to trigger event later.
            $original_thread = Thread::load($this->id);

            $db->query(
                ('UPDATE {thread} SET '
                    . 'username = :user_name, userid = :user_id, '
                    . 'agentname = :agent_name, agentid = :agent_id, '
                    . 'dtmcreated = :created, dtmchatstarted = :chat_started, '
                    . 'dtmmodified = :modified, dtmclosed = :closed, '
                    . 'lrevision = :revision, istate = :state, '
                    . 'invitationstate = :invitation_state, ltoken = :token, '
                    . 'remote = :remote, referer = :referer, '
                    . 'nextagent = :next_agent, locale = :locale, '
                    . 'lastpinguser = :last_ping_user, '
                    . 'lastpingagent = :last_ping_agent, '
                    . 'usertyping = :user_typing, agenttyping = :agent_typing, '
                    . 'shownmessageid = :shown_message_id, '
                    . 'useragent = :user_agent, messagecount = :message_count, '
                    . 'groupid = :group_id '
                . 'WHERE threadid = :thread_id'),
                array(
                    ':thread_id' => $this->id,
                    ':user_name' => $this->userName,
                    ':user_id' => $this->userId,
                    ':agent_name' => $this->agentName,
                    ':agent_id' => $this->agentId,
                    ':created' => $this->created,
                    ':chat_started' => $this->chatStarted,
                    ':modified' => $this->modified,
                    ':closed' => $this->closed,
                    ':revision' => $this->lastRevision,
                    ':state' => $this->state,
                    ':invitation_state' => $this->invitationState,
                    ':token' => $this->lastToken,
                    ':remote' => $this->remote,
                    ':referer' => $this->referer,
                    ':next_agent' => $this->nextAgent,
                    ':locale' => $this->locale,
                    ':last_ping_user' => $this->lastPingUser,
                    ':last_ping_agent' => $this->lastPingAgent,
                    ':user_typing' => $this->userTyping,
                    ':agent_typing' => $this->agentTyping,
                    ':shown_message_id' => $this->shownMessageId,
                    ':user_agent' => $this->userAgent,
                    ':message_count' => $this->messageCount,
                    ':group_id' => $this->groupId,
                )
            );

            $args = array(
                'thread' => $this,
                'original_thread' => $original_thread,
            );
            EventDispatcher::getInstance()->triggerEvent(Events::THREAD_UPDATE, $args);
        }
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
        $operator_name = ($this->locale == get_home_locale())
            ? $operator['vclocalename']
            : $operator['vccommonname'];

        $is_operator_correct = $this->nextAgent == $operator['operatorid']
            || $this->agentId == $operator['operatorid'];

        if ($this->state == self::STATE_WAITING && $is_operator_correct) {
            // Prepare message
            if ($this->nextAgent == $operator['operatorid']) {
                $message_to_post = getlocal(
                    "Operator <strong>{0}</strong> changed operator <strong>{1}</strong>",
                    array($operator_name, $this->agentName),
                    $this->locale,
                    true
                );
            } else {
                $message_to_post = getlocal(
                    "Operator {0} is back",
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
            . "FROM {message} "
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
        }

        // Trigger the "alter" event
        $args = array(
            'messages' => $messages,
            'thread' => $this,
        );
        EventDispatcher::getInstance()->triggerEvent(Events::THREAD_GET_MESSAGES_ALTER, $args);
        $altered_messages = $args['messages'];

        // Get ID of the last message
        foreach ($altered_messages as $msg) {
            if ($msg['id'] > $last_id) {
                $last_id = $msg['id'];
            }
        }

        return $altered_messages;
    }

    /**
     * Send the messsage
     *
     * One can attach arbitrary data to the message by setting 'data' item
     * in the $options array. DO NOT serialize data manually - it will be
     * automatically converted to array and serialized before save in database
     * and unserialized after retrieve form database.
     *
     * One can also set plugin item of the $options array to indicate that
     * message was sent by a plugin.
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::THREAD_POST_MESSAGE}
     * event.
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
     */
    public function postMessage($kind, $message, $options = array())
    {
        $event_args = array(
            'thread' => $this,
            'message_kind' => $kind,
            'message_body' => $message,
            'message_options' => (is_array($options) ? $options : array()),
        );
        EventDispatcher::getInstance()->triggerEvent(Events::THREAD_POST_MESSAGE, $event_args);

        // Send message
        return $this->saveMessage(
            $event_args['message_kind'],
            $event_args['message_body'],
            $event_args['message_options']
        );
    }

    /**
     * Close thread and send closing messages to the conversation members
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::THREAD_CLOSE} event.
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
                getlocal(
                    "Visitor {0} left the chat",
                    array($this->userName),
                    $this->locale,
                    true
                )
            );
        } else {
            if ($this->state == self::STATE_INVITED) {
                $this->postMessage(
                    self::KIND_FOR_AGENT,
                    getlocal(
                        'Operator canceled invitation',
                        null,
                        $this->locale,
                        true
                    )
                );
            } else {
                $this->postMessage(
                    self::KIND_EVENTS,
                    getlocal(
                        "Operator {0} left the chat",
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
            ("SELECT COUNT(*) FROM {message} "
                . "WHERE {message}.threadid = :threadid AND ikind = :kind_user"),
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

            $args = array('thread' => $this);
            EventDispatcher::getInstance()->triggerEvent(Events::THREAD_CLOSE, $args);
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
        // There are states which forbids thread taking. Make sure the current
        // state is not one of them.
        $forbidden_states = array(
            self::STATE_CLOSED,
            self::STATE_LEFT,
            self::STATE_INVITED,
        );
        if (in_array($this->state, $forbidden_states)) {
            return false;
        }

        $is_operator_changed = ($operator['operatorid'] != $this->agentId)
            // Only these states allow operators changing. In other states
            // changing of operator's ID is treated in different ways (join or
            // come back).
            && ($this->state == self::STATE_WAITING || $this->state == self::STATE_CHATTING);

        // For these states we assume that the thread has no operator yet. The
        // check for operator changing is skipped because it will always
        // return positive result.
        $is_operator_joined = ($this->state == self::STATE_LOADING)
            || ($this->state == self::STATE_QUEUE);

        $is_operator_back = ($this->state == self::STATE_WAITING)
            && ($operator['operatorid'] == $this->agentId);

        $message = '';
        $operator_name = ($this->locale == get_home_locale())
            ? $operator['vclocalename']
            : $operator['vccommonname'];

        if ($is_operator_changed) {
            $message = getlocal(
                "Operator <strong>{0}</strong> changed operator <strong>{1}</strong>",
                array($operator_name, $this->agentName),
                $this->locale,
                true
            );
        } elseif ($is_operator_joined) {
            $message = getlocal(
                "Operator {0} joined the chat",
                array($operator_name),
                $this->locale,
                true
            );
        } elseif ($is_operator_back) {
            $message = getlocal(
                "Operator {0} is back",
                array($operator_name),
                $this->locale,
                true
            );
        }

        // Make sure the thread have correct operator and state.
        $this->state = self::STATE_CHATTING;
        $this->nextAgent = 0;
        $this->agentId = $operator['operatorid'];
        $this->agentName = $operator_name;
        if (empty($this->chatStarted)) {
            // This is needed only if the chat was not started yet. Such threads
            // should originally belong to STATE_QUEUE or STATE_LOADING.
            $this->chatStarted = time();
        }
        $this->save();

        // Send message
        if ($message) {
            $this->postMessage(self::KIND_EVENTS, $message);
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
        // Rename only if a new name is really new
        if ($this->userName != $new_name) {
            // Save old name
            $old_name = $this->userName;
            // Rename user
            $this->userName = $new_name;
            $this->save();

            // Send message about renaming
            $message = getlocal(
                "The visitor changed their name <strong>{0}</strong> to <strong>{1}</strong>",
                array($old_name, $new_name),
                $this->locale,
                true
            );
            $this->postMessage(self::KIND_EVENTS, $message);
        }
    }

    /**
     * Sets thread's fields according to the fields from Database.
     *
     * @param array $db_fields Associative array of database fields which keys
     *   are fields names and the values are fields values.
     */
    protected function populateFromDbFields($db_fields)
    {
        $this->id = (int)$db_fields['threadid'];
        $this->userName = $db_fields['username'];
        $this->userId = $db_fields['userid'];
        $this->agentName = $db_fields['agentname'];
        $this->agentId = (int)$db_fields['agentid'];
        $this->created = (int)$db_fields['dtmcreated'];
        $this->chatStarted = (int)$db_fields['dtmchatstarted'];
        $this->modified = (int)$db_fields['dtmmodified'];
        $this->closed = (int)$db_fields['dtmclosed'];
        $this->lastRevision = (int)$db_fields['lrevision'];
        $this->state = (int)$db_fields['istate'];
        $this->invitationState = (int)$db_fields['invitationstate'];
        $this->lastToken = (int)$db_fields['ltoken'];
        $this->remote = $db_fields['remote'];
        $this->referer = $db_fields['referer'];
        $this->nextAgent = (int)$db_fields['nextagent'];
        $this->locale = $db_fields['locale'];
        $this->lastPingUser = (int)$db_fields['lastpinguser'];
        $this->lastPingAgent = (int)$db_fields['lastpingagent'];
        $this->userTyping = $db_fields['usertyping'];
        $this->agentTyping = $db_fields['agenttyping'];
        $this->shownMessageId = (int)$db_fields['shownmessageid'];
        $this->userAgent = $db_fields['useragent'];
        $this->messageCount = $db_fields['messagecount'];
        $this->groupId = (int)$db_fields['groupid'];
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
        $query = "INSERT INTO {message} ("
                . "threadid, ikind, tmessage, tname, agentid, "
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
        $db->query("UPDATE {revision} SET id=LAST_INSERT_ID(id+1)");
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
}
