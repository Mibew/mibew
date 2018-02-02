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

// Import namespaces and classes of the core
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Database;
use Mibew\ProcessLock;
use Mibew\Settings;
use Mibew\Thread;

/**
 * Check invitation state for specified visitior
 *
 * @param int $visitor_id ID of the visitor to check
 * @return array Array of invitation info. It contains following items:
 *  - 'invited': boolean, indicates if the visitor was invited by an operator;
 *  - 'threadid': int, ID of the thread, related with visitor or boolean false
 *    if visitor with specfied ID does not exist.
 */
function invitation_state($visitor_id)
{
    $db = Database::getInstance();
    $db_result = $db->query(
        ("SELECT t.threadid, t.invitationstate, t.istate "
            . "FROM {sitevisitor} v, {thread} t "
            . "WHERE visitorid = ? "
            . "AND t.threadid = v.threadid"),
        array($visitor_id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    $ret = array();
    if (!$db_result) {
        $ret['invited'] = false;
        $ret['threadid'] = false;
    } else {
        $ret['invited'] = ($db_result['istate'] == Thread::STATE_INVITED)
            && ($db_result['invitationstate'] == Thread::INVITATION_WAIT);
        $ret['threadid'] = $db_result['threadid'];
    }

    return $ret;
}

/**
 * Invite visitor by operator
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::INVITATION_CREATE} event.
 *
 * @param int $visitor_id ID of the visitor, who must be invited.
 * @param array $operator Info for operator  who invite the visitor
 * @return Thread|boolean Thread object related with invitation or boolean
 *   false on failure
 */
function invitation_invite($visitor_id, $operator)
{
    // Check if visitor already invited
    $invitation_state = invitation_state($visitor_id);
    if ($invitation_state['invited']) {
        return false;
    }

    // Get visitor info
    $visitor = track_get_visitor_by_id($visitor_id);

    // Get last page visited by the visitor
    $visitor_path = track_get_path($visitor);
    ksort($visitor_path);
    $last_visited_page = array_pop($visitor_path);

    // Get visitor details
    $visitor_details = track_retrieve_details($visitor);

    // Get some operator's info
    $operator_name = get_operator_name($operator);

    // Create thread for invitation
    $thread = new Thread();

    // Populate thread and save it
    $thread->agentId = $operator['operatorid'];
    $thread->agentName = $operator_name;
    $thread->userName = $visitor['username'];
    $thread->remote = $visitor_details['remote_host'];
    $thread->referer = $last_visited_page;
    // User's locale is unknown, set operator locale to the thread
    $thread->locale = get_current_locale();
    $thread->userId = $visitor['userid'];
    $thread->userAgent = $visitor_details['user_agent'];
    $thread->state = Thread::STATE_INVITED;
    $thread->invitationState = Thread::INVITATION_WAIT;
    $thread->save();

    $db = Database::getInstance();
    $db->query(
        ("UPDATE {sitevisitor} set "
            . "invitations = invitations + 1, "
            . "threadid = :thread_id "
            . "WHERE visitorid = :visitor_id"),
        array(
            ':thread_id' => $thread->id,
            ':visitor_id' => $visitor_id,
        )
    );

    // Send some messages
    $thread->postMessage(
        Thread::KIND_FOR_AGENT,
        getlocal(
            'Operator {0} invites visitor at {1} page',
            array($operator_name, $last_visited_page),
            get_current_locale(),
            true
        )
    );
    $thread->postMessage(
        Thread::KIND_AGENT,
        getlocal('Hello, how can I help you?', null, get_current_locale(), true),
        array(
            'name' => $operator_name,
            'operator_id' => $operator['operatorid'],
        )
    );

    // Let plugins know about the invitation.
    $args = array('invitation' => $thread);
    EventDispatcher::getInstance()->triggerEvent(Events::INVITATION_CREATE, $args);

    return $thread;
}

/**
 * Invitation was accepted by visitor
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::INVITATION_ACCEPT} event.
 *
 * @param int $visitor_id ID of the visitor who accept invitation
 * @return Thread|boolean Thread object or boolean false on failure
 */
function invitation_accept($visitor_id)
{
    // Check if visitor was invited
    $invitation_state = invitation_state($visitor_id);
    if (!$invitation_state['invited']) {
        // Visitor was not invited
        return false;
    }

    // Get thread related with the visitor
    $db = Database::getInstance();
    $result = $db->query(
        "SELECT threadid FROM {sitevisitor} WHERE visitorid = :visitor_id",
        array(':visitor_id' => $visitor_id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    if (empty($result['threadid'])) {
        // Something went wrong. There is no thread related with the visitor.
        return false;
    }

    $thread = Thread::load($result['threadid']);
    if (!$thread) {
        // Something went wrong. Cannot load thread.
        return false;
    }

    // Update thread info
    $thread->state = Thread::STATE_CHATTING;
    $thread->invitationState = Thread::INVITATION_ACCEPTED;
    $thread->chatStarted = time();
    $thread->save();

    // Update visitor info
    $db->query(
        ("UPDATE {sitevisitor} SET chats = chats + 1 "
            . "WHERE visitorid = :visitor_id"),
        array(':visitor_id' => $visitor_id)
    );

    $args = array('invitation' => $thread);
    EventDispatcher::getInstance()->triggerEvent(Events::INVITATION_ACCEPT, $args);

    return $thread;
}

/**
 * Inviation was rejected by visitor
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::INVITATION_REJECT} event.
 *
 * @param int $visitor_id ID of the visitor
 */
function invitation_reject($visitor_id)
{
    $visitor = track_get_visitor_by_id($visitor_id);

    // Send message to operator
    $thread = Thread::load($visitor['threadid']);
    if ($thread) {
        $thread->postMessage(
            Thread::KIND_FOR_AGENT,
            getlocal('Visitor rejected invitation', null, get_current_locale(), true)
        );
    }

    $db = Database::getInstance();
    $db->query(
        ("UPDATE {sitevisitor} v, {thread} t SET "
            . "v.threadid = NULL, "
            . "t.invitationstate = :invitation_rejected, "
            . "t.istate = :state_closed, "
            . "t.dtmclosed = :now "
            . "WHERE t.threadid = v.threadid "
            . "AND visitorid = :visitor_id"),
        array(
            ':invitation_rejected' => Thread::INVITATION_REJECTED,
            ':state_closed' => Thread::STATE_CLOSED,
            ':visitor_id' => $visitor_id,
            ':now' => time(),
        )
    );

    $args = array('invitation' => $thread);
    EventDispatcher::getInstance()->triggerEvent(Events::INVITATION_REJECT, $args);
}

/**
 * Close old invitations.
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::INVITATION_IGNORE} event.
 */
function invitation_close_old()
{
    // Run only one instance of cleaning process.
    $lock = new ProcessLock('invitations_close_old');
    if ($lock->get()) {
        // Freeze the time for the whole cleaning process.
        $now = time();

        $db = Database::getInstance();

        // Remove links between visitors and invitations that will be closed.
        $db->query(
            ("UPDATE {sitevisitor} v, {thread} t SET "
                . "v.threadid = NULL "
                . "WHERE t.istate = :state_invited "
                . "AND t.invitationstate = :invitation_wait "
                . "AND (:now - t.dtmcreated) > :lifetime"),
            array(
                ':invitation_wait' => Thread::INVITATION_WAIT,
                ':state_invited' => Thread::STATE_INVITED,
                ':lifetime' => Settings::get('invitation_lifetime'),
                ':now' => $now,
            )
        );

        // Get all invitations to close
        $threads = $db->query(
            ("SELECT * FROM {thread} "
                . "WHERE istate = :state_invited "
                . "AND invitationstate = :invitation_wait "
                . "AND (:now - dtmcreated) > :lifetime"),
            array(
                ':invitation_wait' => Thread::INVITATION_WAIT,
                ':state_invited' => Thread::STATE_INVITED,
                ':lifetime' => Settings::get('invitation_lifetime'),
                ':now' => $now,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Close the invitations
        foreach ($threads as $thread_info) {
            $thread = Thread::createFromDbInfo($thread_info);
            $thread->invitationState = Thread::INVITATION_IGNORED;
            $thread->state = Thread::STATE_CLOSED;
            $thread->closed = $now;
            $thread->save();

            // Notify the operator about autoclosing
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getlocal(
                    'Visitor ignored invitation and it was closed automatically',
                    null,
                    $thread->locale,
                    true
                )
            );

            $args = array('invitation' => $thread);
            EventDispatcher::getInstance()->triggerEvent(Events::INVITATION_IGNORE, $args);

            unset($thread);
        }

        // Release the lock
        $lock->release();
    }
}

/**
 * Prepare data to dispaly invitation
 *
 * @param Thread $thread Thread object related with invitation
 * @return array Array of invitation data
 */
function setup_invitation_view(Thread $thread)
{
    $data = prepare_chat_app_data();

    // Set refresh frequency
    $data['frequency'] = Settings::get('updatefrequency_chat');

    // Create some empty arrays
    $data['invitation'] = array();

    $data['invitation']['thread'] = array(
        'id' => $thread->id,
        'token' => $thread->lastToken,
        'agentId' => $thread->agentId,
        'userId' => $thread->userId,
    );

    $data['invitation']['user'] = array(
        'name' => htmlspecialchars($thread->userName),
        'canChangeName' => false,
        'isAgent' => false,
    );

    $data['startFrom'] = 'invitation';

    return $data;
}
