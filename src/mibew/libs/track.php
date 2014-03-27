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

// Import namespaces and classes of the core
use Mibew\Database;
use Mibew\Settings;
use Mibew\Thread;

function track_visitor($visitor_id, $entry, $referer)
{
    $visitor = track_get_visitor_by_id($visitor_id);

    if (false === $visitor) {
        $visitor = track_visitor_start($entry, $referer);

        return $visitor;
    } else {
        $db = Database::getInstance();
        $db->query(
            "UPDATE {chatsitevisitor} SET lasttime = :now WHERE visitorid = :visitorid",
            array(
                ':visitorid' => $visitor['visitorid'],
                ':now' => time(),
            )
        );
        track_visit_page($visitor['visitorid'], $referer);

        return $visitor['visitorid'];
    }
}

function track_visitor_start($entry, $referer)
{
    $visitor = visitor_from_request();

    $db = Database::getInstance();
    $db->query(
        ("INSERT INTO {chatsitevisitor} ( "
            . "userid, username, firsttime, lasttime, entry,details "
        . ") VALUES ( "
            . ":userid, :username, :now, :now, :entry, :details "
        . ")"),
        array(
            ':userid' => $visitor['id'],
            ':username' => $visitor['name'],
            ':now' => time(),
            ':entry' => $entry,
            ':details' => track_build_details(),
        )
    );

    $id = $db->insertedId();

    if ($id) {
        track_visit_page($id, $referer);
    }

    return $id ? $id : 0;
}

function track_get_visitor_by_id($visitor_id)
{
    $db = Database::getInstance();

    return $db->query(
        "SELECT * FROM {chatsitevisitor} WHERE visitorid = ?",
        array($visitor_id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
}

function track_get_visitor_by_thread_id($thread_id)
{
    $db = Database::getInstance();

    return $db->query(
        "SELECT * FROM {chatsitevisitor} WHERE threadid = ?",
        array($thread_id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
}

/**
 * Load visitor info by user id.
 *
 * @param string $user_id User id
 * @return boolean|array Visitor array or boolean false if visitor not exists
 */
function track_get_visitor_by_user_id($user_id)
{
    $db = Database::getInstance();

    return $db->query(
        "SELECT * FROM {chatsitevisitor} WHERE userid = ?",
        array($user_id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
}

function track_visit_page($visitor_id, $page)
{
    $db = Database::getInstance();

    if (empty($page)) {
        return;
    }
    $last_page = $db->query(
        ("SELECT address "
            . "FROM {visitedpage} "
            . "WHERE visitorid = ? "
            . "ORDER BY visittime DESC "
            . "LIMIT 1"),
        array($visitor_id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
    if ($last_page['address'] != $page) {
        $db->query(
            ("INSERT INTO {visitedpage} ("
                . "visitorid, address, visittime "
            . ") VALUES ( "
                . ":visitorid, :page, :now "
            .")"),
            array(
                ':visitorid' => $visitor_id,
                ':page' => $page,
                ':now' => time(),
            )
        );
    }
}

function track_get_path($visitor)
{
    $db = Database::getInstance();
    $query_result = $db->query(
        "SELECT address, visittime FROM {visitedpage} WHERE visitorid = ?",
        array($visitor['visitorid']),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );
    $result = array();
    foreach ($query_result as $page) {
        $result[$page['visittime']] = $page['address'];
    }

    return $result;
}

function track_build_details()
{
    $result = array(
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'remote_host' => get_remote_host(),
    );

    return serialize($result);
}

function track_retrieve_details($visitor)
{
    return unserialize($visitor['details']);
}

/**
 * Remove old visitors
 */
function track_remove_old_visitors()
{
    $db = Database::getInstance();

    // Remove associations of visitors with closed threads
    $db->query(
        "UPDATE {chatsitevisitor} SET threadid = NULL "
        . "WHERE threadid IS NOT NULL AND "
        . "(SELECT count(*) FROM {chatthread} "
        . "WHERE threadid = {chatsitevisitor}.threadid "
        . "AND istate <> " . Thread::STATE_CLOSED . " "
        . "AND istate <> " . Thread::STATE_LEFT . ") = 0 "
    );

    // Remove old visitors
    $db->query(
        ("DELETE FROM {chatsitevisitor} "
            . "WHERE (:now - lasttime) > :lifetime "
            . "AND threadid IS NULL"),
        array(
            ':lifetime' => Settings::get('tracking_lifetime'),
            ':now' => time(),
        )
    );
}

/**
 * Remove old tracks
 */
function track_remove_old_tracks()
{
    $db = Database::getInstance();

    // Remove old visitors' tracks
    $db->query(
        ("DELETE FROM {visitedpage} "
            . "WHERE (:now - visittime) > :lifetime "
            // Remove only tracks that are included in statistics
            . "AND calculated = 1 "
            . "AND visitorid NOT IN (SELECT visitorid FROM {chatsitevisitor}) "),
        array(
            ':lifetime' => Settings::get('tracking_lifetime'),
            ':now' => time(),
        )
    );
}

/*
 * Return user id by visitor id.
 *
 * @param int $visitor_id Id of the visitor
 * @return string|boolean user id or boolean false if there is no visitor with
 * specified visitor id
 */
function track_get_user_id($visitor_id)
{
    $visitor = track_get_visitor_by_id($visitor_id);
    if (!$visitor) {
        return false;
    }

    return $visitor['userid'];
}

/**
 * Bind chat thread with visitor
 *
 * @param string $user_id User ID ({chatsitevisitor}.userid field) of the
 * visitor.
 * @param Thread $thread Chat thread object
 */
function track_visitor_bind_thread($user_id, $thread)
{
    $db = Database::getInstance();
    $db->query(
        ('UPDATE {chatsitevisitor} '
            . 'SET threadid = :thread_id '
            . 'WHERE userid = :user_id'),
        array(
            ':thread_id' => $thread->id,
            ':user_id' => $user_id,
        )
    );
}
