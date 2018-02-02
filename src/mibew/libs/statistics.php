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
use Mibew\Database;
use Mibew\Thread;

/**
 * Loads statistics about system usage aggregated by dates for the time between
 * $start and $end timestamps.
 *
 * @param int $start Timestamp for beginning of the interval of interest.
 * @param int $end Timestamp for ending of the interval of interest.
 * @return array Associative array with the following keys:
 *  - "records": array of associative arrays, set of statistics records
 *  - "total": associative array, aggregated statistics for the interval of
 *    interest.
 */
function get_by_date_statistics($start, $end)
{
    $db = Database::getInstance();

    // Get statistics records aggregated by date
    $records = $db->query(
        ("SELECT DATE(FROM_UNIXTIME(date)) AS date, "
            . "threads, "
            . "missedthreads, "
            . "sentinvitations, "
            . "acceptedinvitations, "
            . "rejectedinvitations, "
            . "ignoredinvitations, "
            . "operatormessages AS agents, "
            . "usermessages AS users, "
            . "averagewaitingtime AS avgwaitingtime, "
            . "averagechattime AS avgchattime "
        . "FROM {threadstatistics} s "
        . "WHERE s.date >= :start "
            . "AND s.date < :end "
        . "ORDER BY s.date DESC"),
        array(
            ':start' => $start,
            ':end' => $end,
        ),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    // Get statistics aggregated for all accessed interval
    $total = $db->query(
        ("SELECT SUM(threads) AS threads, "
            . "SUM(missedthreads) AS missedthreads, "
            . "SUM(sentinvitations) AS sentinvitations, "
            . "SUM(acceptedinvitations) AS acceptedinvitations, "
            . "SUM(rejectedinvitations) AS rejectedinvitations, "
            . "SUM(ignoredinvitations) AS ignoredinvitations, "
            . "SUM(operatormessages) AS agents, "
            . "SUM(usermessages) AS users, "
            . "ROUND(SUM(averagewaitingtime * s.threads) / SUM(s.threads),1) AS avgwaitingtime, "
            . "ROUND(SUM(averagechattime * s.threads) / SUM(s.threads),1) AS avgchattime "
        . "FROM {threadstatistics} s "
        . "WHERE s.date >= :start "
            . "AND s.date < :end"),
        array(
            ':start' => $start,
            ':end' => $end,
        ),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    return array(
        'records' => $records,
        'total' => $total,
    );
}

/**
 * Loads statistics about operators for the time between $start and $end
 * timestamps.
 *
 * @param int $start Timestamp for beginning of the interval of interest.
 * @param int $end Timestamp for ending of the interval of interest.
 * @return array Set of statistics records
 */
function get_by_operator_statistics($start, $end)
{
    $db = Database::getInstance();
    $result = $db->query(
        ("SELECT o.vclocalename AS name, "
            . "SUM(s.threads) AS threads, "
            . "SUM(s.messages) AS msgs, "
            . "ROUND( "
                    . "SUM(s.averagelength * s.messages) / SUM(s.messages), "
                . "1) AS avglen, "
            . "SUM(sentinvitations) AS sentinvitations, "
            . "SUM(acceptedinvitations) AS acceptedinvitations, "
            . "SUM(rejectedinvitations) AS rejectedinvitations, "
            . "SUM(ignoredinvitations) AS ignoredinvitations "
        . "FROM {operatorstatistics} s, {operator} o "
        . "WHERE s.operatorid = o.operatorid "
            . "AND s.date >= :start "
            . "AND s.date < :end "
        . "GROUP BY s.operatorid, o.vclocalename"),
        array(
            ':start' => $start,
            ':end' => $end,
        ),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    return $result;
}

/**
 * Loads statistics about pages for the time between $start and $end timestamps.
 *
 * @param int $start Timestamp for beginning of the interval of interest.
 * @param int $end Timestamp for ending of the interval of interest.
 * @return array Set of statistics records
 */
function get_by_page_statistics($start, $end)
{
    $db = Database::getInstance();
    $result = $db->query(
        ("SELECT SUM(visits) as visittimes, "
            . "address, "
            . "SUM(chats) as chattimes, "
            . "SUM(sentinvitations) AS sentinvitations, "
            . "SUM(acceptedinvitations) AS acceptedinvitations, "
            . "SUM(rejectedinvitations) AS rejectedinvitations, "
            . "SUM(ignoredinvitations) AS ignoredinvitations "
        . "FROM {visitedpagestatistics} "
        . "WHERE date >= :start "
            . "AND date < :end "
        . "GROUP BY address"),
        array(':start' => $start, ':end' => $end),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    return $result;
}

/**
 * Calculate aggregated 'by thread' statistics
 */
function calculate_thread_statistics()
{
    // Prepare database
    $db = Database::getInstance();
    $db_throw_exceptions = $db->throwExceptions(true);

    try {
        // Start transaction
        $db->query('START TRANSACTION');

        // Get last record date
        $result = $db->query(
            "SELECT MAX(date) as start FROM {threadstatistics}",
            array(),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        $start = empty($result['start']) ? 0 : $result['start'];
        $today = floor(time() / STATISTICS_AGGREGATION_INTERVAL) * STATISTICS_AGGREGATION_INTERVAL;

        // Calculate statistics
        // Get base threads info
        $db_results = $db->query(
            ("SELECT (FLOOR(t.dtmcreated / :interval) * :interval) AS date, "
                . "COUNT(t.threadid) AS threads, "
                . "SUM(tmp.operator_msgs) AS operator_msgs, "
                . "SUM(tmp.user_msgs) AS user_msgs, "
                // Prevent negative values of avgchattime field.
                // If avgchattime < 0 it becomes to zero.
                // For random value 'a' result of expression ((abs(a) + a) / 2)
                // equals to 'a' if 'a' more than zero
                // and equals to zero otherwise
                . "ROUND(AVG( "
                    . "ABS(tmp.last_msg_time - t.dtmchatstarted) + "
                        . "(tmp.last_msg_time - t.dtmchatstarted) "
                . ")/2,1) AS avg_chat_time "
            . "FROM {thread} t, "
            . "(SELECT SUM(m.ikind = :kind_agent) AS operator_msgs, "
                    . "SUM(m.ikind = :kind_user) AS user_msgs, "
                    . "MAX(m.dtmcreated) as last_msg_time, "
                    . "threadid "
                . "FROM {message} m "
                // Calculate only users' and operators' messages
                . "WHERE m.ikind = :kind_user "
                    . "OR m.ikind = :kind_agent "
                . "GROUP BY m.threadid) tmp "
            . "WHERE t.threadid = tmp.threadid "
                . "AND (t.dtmcreated - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > t.dtmcreated "
                // Ignore threads when operator does not start chat
                . "AND t.dtmchatstarted <> 0 "
                // Ignore not accepted invitations
                . "AND (t.invitationstate = :not_invited "
                    . "OR t.invitationstate = :invitation_accepted) "
            . "GROUP BY date"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':not_invited' => Thread::INVITATION_NOT_INVITED,
                ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
                ':kind_agent' => Thread::KIND_AGENT,
                ':kind_user' => Thread::KIND_USER,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Store statistics data
        $statistics = extend_statistics_info(
            array(),
            $db_results,
            array('date')
        );

        // Get info about missed threads
        $db_results = $db->query(
            ("SELECT (FLOOR(dtmcreated / :interval) * :interval) AS date, "
                . "COUNT(*) as missed_threads "
            . "FROM {thread} "
            . "WHERE (dtmcreated - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > dtmcreated "
                // Ignore threads when operator does not start chat
                . "AND dtmchatstarted = 0 "
                // Ignore not accepted invitations
                . "AND invitationstate = :not_invited "
            . "GROUP BY date ORDER BY date DESC"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':not_invited' => Thread::INVITATION_NOT_INVITED,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Add info about missed threads to statistics data
        $statistics = extend_statistics_info(
            $statistics,
            $db_results,
            array('date')
        );

        // Get info about average waiting time.
        $db_results = $db->query(
            ("SELECT (FLOOR(dtmcreated / :interval) * :interval) AS date, "
                . "ROUND(AVG(dtmchatstarted-dtmcreated),1) AS avg_waiting_time "
            . "FROM {thread} "
            . "WHERE (dtmcreated - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > dtmcreated "
                // Ignore threads when operator does not start chat
                . "AND dtmchatstarted <> 0 "
                // Ignore all invitations
                . "AND invitationstate = :not_invited "
            . "GROUP BY date"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':not_invited' => Thread::INVITATION_NOT_INVITED,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Add average waiting time to statistics data
        $statistics = extend_statistics_info(
            $statistics,
            $db_results,
            array('date')
        );

        // Get invitation info
        $db_results = $db->query(
            ("SELECT (FLOOR(dtmcreated / :interval) * :interval) AS date, "
                . "COUNT(*) AS invitations_sent, "
                . "SUM(invitationstate = :invitation_accepted) AS invitations_accepted, "
                . "SUM(invitationstate = :invitation_rejected) AS invitations_rejected, "
                . "SUM(invitationstate = :invitation_ignored) AS invitations_ignored "
            . "FROM {thread} "
            . "WHERE (dtmcreated - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > dtmcreated "
                . "AND (invitationstate = :invitation_accepted "
                    . "OR invitationstate = :invitation_rejected "
                    . "OR invitationstate = :invitation_ignored) "
            . "GROUP BY date"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
                ':invitation_rejected' => Thread::INVITATION_REJECTED,
                ':invitation_ignored' => Thread::INVITATION_IGNORED,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Add invitation info to statistics data
        $statistics = extend_statistics_info(
            $statistics,
            $db_results,
            array('date')
        );

        // Sort statistics by date before save it in the database
        ksort($statistics);

        foreach ($statistics as $row) {
            // Add default values
            $row += array(
                'threads' => 0,
                'missed_threads' => 0,
                'operator_msgs' => 0,
                'user_msgs' => 0,
                'avg_chat_time' => 0,
                'avg_waiting_time' => 0,
                'invitations_sent' => 0,
                'invitations_accepted' => 0,
                'invitations_rejected' => 0,
                'invitations_ignored' => 0,
            );

            // Prepare data for insert
            $insert_data = array();
            foreach ($row as $field_name => $field_value) {
                $insert_data[':' . $field_name] = $field_value;
            }

            // Store data in database
            $db->query(
                ("INSERT INTO {threadstatistics} ("
                    . "date, threads, missedthreads, sentinvitations, "
                    . "acceptedinvitations, rejectedinvitations, "
                    . "ignoredinvitations, operatormessages, usermessages, "
                    . "averagewaitingtime, averagechattime "
                . ") VALUES ("
                    . ":date, :threads, :missed_threads, :invitations_sent, "
                    . ":invitations_accepted, :invitations_rejected, "
                    . ":invitations_ignored, :operator_msgs, :user_msgs, "
                    . ":avg_waiting_time, :avg_chat_time "
                . ")"),
                $insert_data
            );
        }
    } catch (Exception $e) {
        // Something went wrong: warn and rollback transaction.
        trigger_error(
            'Thread statistics calculating faild: ' . $e->getMessage(),
            E_USER_WARNING
        );
        $db->query('ROLLBACK');

        // Set throw exceptions back
        $db->throwExceptions($db_throw_exceptions);

        return;
    }

    // Commit transaction
    $db->query('COMMIT');

    // Set throw exceptions back
    $db->throwExceptions($db_throw_exceptions);
}

/**
 * Calculate aggregated 'by operator' statistics
 */
function calculate_operator_statistics()
{
    // Prepare database
    $db = Database::getInstance();
    $db_throw_exceptions = $db->throwExceptions(true);

    try {
        // Start transaction
        $db->query('START TRANSACTION');

        // Get last record date
        $result = $db->query(
            "SELECT MAX(date) as start FROM {operatorstatistics}",
            array(),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        $start = empty($result['start']) ? 0 : $result['start'];
        $today = floor(time() / STATISTICS_AGGREGATION_INTERVAL) * STATISTICS_AGGREGATION_INTERVAL;

        // Caclculate statistics
        // Get base operator's info
        $db_results = $db->query(
            ("SELECT (FLOOR(m.dtmcreated / :interval) * :interval) AS date, "
                . "m.agentid AS operator_id, "
                . "COUNT(distinct m.threadid) AS threads, "
                . "COUNT(m.messageid) AS messages, "
                . "AVG(CHAR_LENGTH(m.tmessage)) AS avg_msg_length "
                // Use {message} as base table because of one thread can
                // be related with more than one operator (they can change each
                // other during conversation).
            . "FROM {message} m, {thread} t "
            . "WHERE m.ikind = :kind_agent "
                . "AND  m.threadid = t.threadid "
                . "AND (m.dtmcreated - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > m.dtmcreated "
                // Ignore not accepted invitations
                . "AND (t.invitationstate = :not_invited "
                    . "OR t.invitationstate = :invitation_accepted) "
            . "GROUP BY date, operator_id"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':not_invited' => Thread::INVITATION_NOT_INVITED,
                ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
                ':kind_agent' => Thread::KIND_AGENT,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Store retrieved data as statistics info
        $statistics = extend_statistics_info(
            array(),
            $db_results,
            array('date', 'operator_id')
        );

        // Get info about invitations
        $db_results = $db->query(
            ("SELECT (FLOOR(dtmcreated / :interval) * :interval) AS date, "
                . "agentid AS operator_id, "
                . "COUNT(threadid) AS invitations_sent, "
                . "SUM(invitationstate = :invitation_accepted) AS invitations_accepted, "
                . "SUM(invitationstate = :invitation_rejected) AS invitations_rejected, "
                . "SUM(invitationstate = :invitation_ignored) AS invitations_ignored "
            . "FROM {thread} "
            . "WHERE (dtmcreated - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > dtmcreated "
                // Check if thread has related operator
                . "AND agentid != 0 "
                // Ignore not accepted invitations
                . "AND (invitationstate = :invitation_accepted "
                    . "OR invitationstate = :invitation_rejected "
                    . "OR invitationstate = :invitation_ignored) "
            . "GROUP BY date, operator_id"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
                ':invitation_rejected' => Thread::INVITATION_REJECTED,
                ':invitation_ignored' => Thread::INVITATION_IGNORED,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Store retrieved data as statistics info
        $statistics = extend_statistics_info(
            $statistics,
            $db_results,
            array('date', 'operator_id')
        );

        // Sort statistics by date before save it in the database
        ksort($statistics);

        foreach ($statistics as $row) {
            // Set default values
            $row += array(
                'threads' => 0,
                'messages' => 0,
                'avg_msg_length' => 0,
                'invitations_sent' => 0,
                'invitations_accepted' => 0,
                'invitations_rejected' => 0,
                'invitations_ignored' => 0,
            );

            // Prepare data for insert
            $insert_data = array();
            foreach ($row as $field_name => $field_value) {
                $insert_data[':' . $field_name] = $field_value;
            }

            $db->query(
                ("INSERT INTO {operatorstatistics} ("
                    . "date, operatorid, threads, messages, averagelength, "
                    . "sentinvitations, acceptedinvitations, "
                    . "rejectedinvitations, ignoredinvitations "
                . ") VALUES ("
                    . ":date, :operator_id, :threads, :messages, "
                    . ":avg_msg_length, :invitations_sent, "
                    . ":invitations_accepted, :invitations_rejected, "
                    . ":invitations_ignored "
                . ")"),
                $insert_data
            );
        }
    } catch (Exception $e) {
        // Something went wrong: warn and rollback transaction.
        trigger_error(
            'Operator statistics calculating faild: ' . $e->getMessage(),
            E_USER_WARNING
        );
        $db->query('ROLLBACK');

        // Set throw exceptions back
        $db->throwExceptions($db_throw_exceptions);

        return;
    }

    // Commit transaction
    $db->query('COMMIT');

    // Set throw exceptions back
    $db->throwExceptions($db_throw_exceptions);
}

/**
 * Calculate aggregated 'by page' statistics
 */
function calculate_page_statistics()
{
    // Prepare database
    $db = Database::getInstance();
    $db_throw_exceptions = $db->throwExceptions(true);

    try {
        // Start transaction
        $db->query('START TRANSACTION');

        // Get last record date
        $result = $db->query(
            "SELECT MAX(date) as start FROM {visitedpagestatistics}",
            array(),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        $start = empty($result['start']) ? 0 : $result['start'];
        $today = floor(time() / STATISTICS_AGGREGATION_INTERVAL) * STATISTICS_AGGREGATION_INTERVAL;

        $statistics = array();

        // Calculate statistics
        // Get main pages info
        $db_results = $db->query(
            ("SELECT FLOOR(visittime / :interval) * :interval AS date, "
                . "address, "
                . "COUNT(DISTINCT pageid) AS visits "
            . "FROM {visitedpage} "
            . "WHERE calculated = 0 "
                . "AND (visittime - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > visittime "
            . "GROUP BY date, address"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Store info in statistics data
        $statistics = extend_statistics_info(
            array(),
            $db_results,
            array('date', 'address')
        );

        // Get total chats count
        $db_results = $db->query(
            ("SELECT FLOOR(p.visittime / :interval) * :interval AS date, "
                . "p.address AS address, "
                . "COUNT(DISTINCT t.threadid) AS chats "
            . "FROM {visitedpage} p, {thread} t, "
                . "(SELECT "
                    . "COUNT(*) AS msgs, "
                    . "m.threadid "
                . "FROM {message} m "
                . "WHERE m.ikind = :kind_user OR m.ikind = :kind_agent "
                . "GROUP BY m.threadid) tmp "
            . "WHERE t.referer = p.address "
                . "AND p.calculated = 0 "
                . "AND t.threadid = tmp.threadid "
                . "AND tmp.msgs > 0 "
                . "AND t.dtmchatstarted <> 0 "
                . "AND (p.visittime - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > p.visittime "
                . "AND DATE(FROM_UNIXTIME(p.visittime)) "
                    . "= DATE(FROM_UNIXTIME(t.dtmcreated)) "
                . "AND (t.invitationstate = :not_invited "
                    . "OR t.invitationstate = :invitation_accepted) "
            . "GROUP BY date, address"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':not_invited' => Thread::INVITATION_NOT_INVITED,
                ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
                ':kind_agent' => Thread::KIND_AGENT,
                ':kind_user' => Thread::KIND_USER,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Store info in statistics data
        $statistics = extend_statistics_info(
            $statistics,
            $db_results,
            array('date', 'address')
        );

        // Get info about accepted invitations
        $db_results = $db->query(
            ("SELECT FLOOR(p.visittime / :interval) * :interval AS date, "
                . "p.address AS address, "
                . "COUNT(DISTINCT t.threadid) AS invitations_accepted "
            . "FROM {visitedpage} p, {thread} t "
            . "WHERE t.referer = p.address "
                . "AND p.calculated = 0 "
                . "AND (p.visittime - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > p.visittime "
                . "AND DATE(FROM_UNIXTIME(p.visittime)) "
                    . "= DATE(FROM_UNIXTIME(t.dtmcreated)) "
                . "AND t.invitationstate = :invitation_accepted "
            . "GROUP BY date, address"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Store info in statistics data
        $statistics = extend_statistics_info(
            $statistics,
            $db_results,
            array('date', 'address')
        );

        // Get info about rejected invitations
        $db_results = $db->query(
            ("SELECT FLOOR(p.visittime / :interval) * :interval AS date, "
                . "p.address AS address, "
                . "COUNT(DISTINCT t.threadid) AS invitations_rejected "
            . "FROM {visitedpage} p, {thread} t "
            . "WHERE t.referer = p.address "
                . "AND p.calculated = 0 "
                . "AND (p.visittime - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > p.visittime "
                . "AND DATE(FROM_UNIXTIME(p.visittime)) "
                    . "= DATE(FROM_UNIXTIME(t.dtmcreated)) "
                . "AND t.invitationstate = :invitation_rejected "
            . "GROUP BY date, address"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':invitation_rejected' => Thread::INVITATION_REJECTED,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Store info in statistics data
        $statistics = extend_statistics_info(
            $statistics,
            $db_results,
            array('date', 'address')
        );

        // Get info about ignored invitations
        $db_results = $db->query(
            ("SELECT FLOOR(p.visittime / :interval) * :interval AS date, "
                . "p.address AS address, "
                . "COUNT(DISTINCT t.threadid) AS invitations_ignored "
            . "FROM {visitedpage} p, {thread} t "
            . "WHERE t.referer = p.address "
                . "AND p.calculated = 0 "
                . "AND (p.visittime - :start) > :interval "
                // Calculate statistics only for yesterday
                . "AND :today > p.visittime "
                . "AND DATE(FROM_UNIXTIME(p.visittime)) "
                    . "= DATE(FROM_UNIXTIME(t.dtmcreated)) "
                . "AND t.invitationstate = :invitation_ignored "
            . "GROUP BY date, address"),
            array(
                ':start' => $start,
                ':today' => $today,
                ':interval' => STATISTICS_AGGREGATION_INTERVAL,
                ':invitation_ignored' => Thread::INVITATION_IGNORED,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        // Store info in statistics data
        $statistics = extend_statistics_info(
            $statistics,
            $db_results,
            array('date', 'address')
        );

        // Sort statistics by date before save it in the database
        ksort($statistics);

        foreach ($statistics as $row) {
            // Set default values
            $row += array(
                'visits' => 0,
                'chats' => 0,
                'invitations_accepted' => 0,
                'invitations_rejected' => 0,
                'invitations_ignored' => 0,
            );

            $row['invitations_sent'] = $row['invitations_accepted']
                + $row['invitations_rejected']
                + $row['invitations_ignored'];

            // Prepare data for insert
            $insert_data = array();
            foreach ($row as $field_name => $field_value) {
                $insert_data[':' . $field_name] = $field_value;
            }

            $db->query(
                ("INSERT INTO {visitedpagestatistics} ("
                    . "date, address, visits, chats, "
                    . "sentinvitations, acceptedinvitations, "
                    . "rejectedinvitations, ignoredinvitations "
                . ") VALUES ("
                    . ":date, :address, :visits, :chats, :invitations_sent, "
                    . ":invitations_accepted, :invitations_rejected, "
                    . ":invitations_ignored "
                . ")"),
                $insert_data
            );
        }

        // Mark all visited pages as 'calculated'
        $db->query(
            ("UPDATE {visitedpage} SET calculated = 1 "
                . "WHERE :today > visittime "
                . "AND calculated = 0"),
            array(
                ':today' => $today
            )
        );

        // Remove old tracks from the system
        track_remove_old_tracks();
    } catch (Exception $e) {
        // Something went wrong: warn and rollback transaction.
        trigger_error(
            'Page statistics calculating faild: ' . $e->getMessage(),
            E_USER_WARNING
        );
        $db->query('ROLLBACK');

        // Set throw exceptions back
        $db->throwExceptions($db_throw_exceptions);

        return;
    }

    // Commit transaction
    $db->query('COMMIT');

    // Set throw exceptions back
    $db->throwExceptions($db_throw_exceptions);
}

/**
 * Add info from $additional_info to $stat_info using specified keys.
 *
 * Triggers an error with E_WARNING level if row of $additional_info array does
 * not contain one of specified keys.
 * @param array $stat_info Statistics info
 * @param array $additional_info Data that must be added to statistics info
 * @param array $keys List of keys.
 * @return array|boolean Extended statistics info or boolean false on failure
 */
function extend_statistics_info($stat_info, $additional_info, $keys)
{
    $result = $stat_info;
    foreach ($additional_info as $row) {
        // Build key field
        $key_field = array();
        foreach ($keys as $key) {
            if (!array_key_exists($key, $row)) {
                trigger_error(
                    "There is no '{$key}' key in additional_info row!",
                    E_USER_WARNING
                );

                return false;
            }
            $key_field[] = $row[$key];
        }
        $key_field = implode('_', $key_field);

        // Index info
        if (empty($result[$key_field])) {
            $result[$key_field] = array();
        }

        // Extend info
        $result[$key_field] += $row;
    }

    return $result;
}
