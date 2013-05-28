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

function get_statistics_query($type)
{
	$query = $_SERVER['QUERY_STRING'];
	if (! empty($query)) {
		$query = '?'.$query;
		$query = preg_replace("/\?type=\w+\&/", "?", $query);
		$query = preg_replace("/(\?|\&)type=\w+/", "", $query);
	}
	$query .= strstr($query, "?") ? "&type=$type" : "?type=$type";
	return $query;
}

function setup_statistics_tabs($active)
{
	global $page, $webimroot;
	$page['tabs'] = array(
		getlocal("report.bydate.title") => $active != 0 ? "$webimroot/operator/statistics.php".get_statistics_query('bydate') : "",
		getlocal("report.byoperator.title") => $active != 1 ? "$webimroot/operator/statistics.php".get_statistics_query('byagent') : ""
	);
	if (Settings::get('enabletracking')) {
		$page['tabs'][getlocal("report.bypage.title")] = ($active != 2 ? "$webimroot/operator/statistics.php".get_statistics_query('bypage') : "");
	}
}

/**
 * Calculate aggregated 'by thread' statistics
 */
function calculate_thread_statistics() {
	// Prepare database
	$db = Database::getInstance();
	$db_throw_exceptions = $db->throwExeptions(true);

	try {
		// Start transaction
		$db->query('START TRANSACTION');

		// Get last record date
		$result = $db->query(
			"SELECT MAX(date) as start FROM {chatthreadstatistics}",
			array(),
			array('return_rows' => Database::RETURN_ONE_ROW)
		);

		$start = empty($result['start']) ? 0 : $result['start'];
		$today = floor(time() / (24*60*60)) * 24*60*60;

		// Calculate statistics
		// Get base threads info
		$db_results = $db->query(
			"SELECT (FLOOR(t.dtmcreated / (24*60*60)) * 24*60*60) AS date, " .
				"COUNT(t.threadid) AS threads, " .
				"SUM(tmp.operator_msgs) AS operator_msgs, " .
				"SUM(tmp.user_msgs) AS user_msgs, " .
				// Prevent negative values of avgchattime field.
				// If avgchattime < 0 it becomes to zero.
				// For random value 'a' result of expression ((abs(a) + a) / 2)
				// equals to 'a' if 'a' more than zero
				// and equals to zero otherwise
				"ROUND(AVG( " .
					"ABS(tmp.last_msg_time - t.dtmchatstarted) + " .
					"(tmp.last_msg_time - t.dtmchatstarted) " .
				")/2,1) as avg_chat_time " .
			"FROM {chatthread} t, " .
				"(SELECT SUM(m.ikind = :kind_agent) AS operator_msgs, " .
					"SUM(m.ikind = :kind_user) AS user_msgs, " .
					"MAX(m.dtmcreated) as last_msg_time, " .
					"threadid " .
				"FROM {indexedchatmessage} m " .
				// Calculate only users' and operators' messages
				"WHERE m.ikind = :kind_user " .
					"OR m.ikind = :kind_agent " .
				"GROUP BY m.threadid) tmp " .
			"WHERE t.threadid = tmp.threadid " .
				"AND (t.dtmcreated - :start) > 24*60*60 " .
				// Calculate statistics only for threads that older than one day
				"AND (:today - t.dtmcreated) > 24*60*60 " .
				// Ignore threads when operator does not start chat
				"AND t.dtmchatstarted <> 0 " .
				// Ignore not accepted invitations
				"AND (t.invitationstate = :not_invited " .
					"OR t.invitationstate = :invitation_accepted) " .
			"GROUP BY date",
			array(
				':start' => $start,
				':today' => $today,
				':not_invited' => Thread::INVITATION_NOT_INVITED,
				':invitation_accepted' => Thread::INVITATION_ACCEPTED,
				':kind_agent' => Thread::KIND_AGENT,
				':kind_user' => Thread::KIND_USER
			),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		// Store statistics data
		$statistics = extend_statistics_info(array(), $db_results);

		// Get info about missed threads
		$db_results = $db->query(
			"SELECT (FLOOR(dtmcreated / (24*60*60)) * 24*60*60) AS date, " .
				"COUNT(*) as missed_threads " .
			"FROM {chatthread} " .
			"WHERE (dtmcreated - :start) > 24*60*60 " .
				// Calculate statistics only for threads that older than one day
				"AND (:today - dtmcreated) > 24*60*60 " .
				// Ignore threads when operator does not start chat
				"AND dtmchatstarted = 0 " .
				// Ignore not accepted invitations
				"AND invitationstate = :not_invited " .
			"GROUP BY date ORDER BY date DESC",
			array(
				':start' => $start,
				':today' => $today,
				':not_invited' => Thread::INVITATION_NOT_INVITED
			),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		// Add average waiting time to statistics data
		$statistics = extend_statistics_info($statistics, $db_results);

		// Get info about average chat time and missed threads count.
		$db_results = $db->query(
			"SELECT (FLOOR(dtmcreated / (24*60*60)) * 24*60*60) AS date, " .
				"ROUND(AVG(dtmchatstarted-dtmcreated),1) AS avg_waiting_time,
				SUM(dtmchatstarted = 0) AS missed_threads " .
			"FROM {chatthread} " .
			"WHERE (dtmcreated - :start) > 24*60*60 " .
				// Calculate statistics only for threads that older than one day
				"AND (:today - dtmcreated) > 24*60*60 " .
				// Ignore threads when operator does not start chat
				"AND dtmchatstarted <> 0 " .
				// Ignore all invitations
				"AND invitationstate = :not_invited " .
			"GROUP BY date",
			array(
				':start' => $start,
				':today' => $today,
				':not_invited' => Thread::INVITATION_NOT_INVITED
			),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		// Add average waiting time to statistics data
		$statistics = extend_statistics_info($statistics, $db_results);

		// Get invitation info
		$db_results = $db->query(
			"SELECT (FLOOR(dtmcreated / (24*60*60)) * 24*60*60) AS date, " .
				"COUNT(*) AS invitation_sent, " .
				"SUM(invitationstate = :invitation_accepted) AS invitation_accepted, " .
				"SUM(invitationstate = :invitation_rejected) AS invitation_rejected, " .
				"SUM(invitationstate = :invitation_ignored) AS invitation_ignored " .
			"FROM {chatthread} " .
			"WHERE (dtmcreated - :start) > 24*60*60 " .
				// Calculate statistics only for threads that older than one day
				"AND (:today - dtmcreated) > 24*60*60 " .
				"AND (invitationstate = :invitation_accepted " .
					"OR invitationstate = :invitation_rejected " .
					"OR invitationstate = :invitation_ignored) " .
			"GROUP BY date",
			array(
				':start' => $start,
				':today' => $today,
				':invitation_accepted' => Thread::INVITATION_ACCEPTED,
				':invitation_rejected' => Thread::INVITATION_REJECTED,
				':invitation_ignored' => Thread::INVITATION_IGNORED
			),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		// Add invitation info to statistics data
		$statistics = extend_statistics_info($statistics, $db_results);

		// Sort statistics by date before save it in the database
		ksort($statistics);

		foreach($statistics as $row) {
			// Add default values
			$row += array(
				'threads' => 0,
				'missed_threads' => 0,
				'operator_msgs' => 0,
				'user_msgs' => 0,
				'avg_chat_time' => 0,
				'avg_waiting_time' => 0,
				'invitation_sent' => 0,
				'invitation_accepted' => 0,
				'invitation_rejected' => 0,
				'invitation_ignored' => 0
			);

			// Prepare data for insert
			$insert_data = array();
			foreach($row as $field_name => $field_value) {
				$insert_data[':' . $field_name] = $field_value;
			}

			// Store data in database
			$db->query(
				"INSERT INTO {chatthreadstatistics} (" .
					"date, threads, missedthreads, sentinvitations, " .
					"acceptedinvitations, rejectedinvitations, " .
					"ignoredinvitations, operatormessages, usermessages, " .
					"averagewaitingtime, averagechattime " .
				") VALUES (" .
					":date, :threads, :missed_threads, :invitation_sent, " .
					":invitation_accepted, :invitation_rejected, " .
					":invitation_ignored, :operator_msgs, :user_msgs, " .
					":avg_waiting_time, :avg_chat_time " .
				")",
				$insert_data
			);
		}
	} catch(Exception $e) {
		// Something went wrong: warn and rollback transaction.
		trigger_error(
			'Thread statistics calculating faild: ' . $e->getMessage(),
			E_USER_WARNING
		);
		$db->query('ROLLBACK');

		// Set throw exceptions back
		$db->throwExeptions($db_throw_exceptions);
		return;
	}

	// Commit transaction
	$db->query('COMMIT');

	// Set throw exceptions back
	$db->throwExeptions($db_throw_exceptions);
}

/**
 * Calculate aggregated 'by operator' statistics
 */
function calculate_operator_statistics() {
	// Prepare database
	$db = Database::getInstance();
	$db_throw_exceptions = $db->throwExeptions(true);

	try {
		// Start transaction
		$db->query('START TRANSACTION');

		// Get last record date
		$result = $db->query(
			"SELECT MAX(date) as start FROM {chatoperatorstatistics}",
			array(),
			array('return_rows' => Database::RETURN_ONE_ROW)
		);

		$start = empty($result['start']) ? 0 : $result['start'];
		$today = floor(time() / (24*60*60)) * 24*60*60;

		// Caclculate statistics
		$db->query(
			"INSERT INTO {chatoperatorstatistics} ( " .
				"date, operatorid, threads, messages, averagelength" .
			") SELECT (FLOOR(m.dtmcreated / (24*60*60)) * 24*60*60) AS date, " .
				"o.operatorid AS opid, " .
				"COUNT(distinct m.threadid) AS threads, " .
				"SUM(m.ikind = :kind_agent) AS msgs, " .
				"AVG(CHAR_LENGTH(m.tmessage)) AS avglen " .
			"FROM {indexedchatmessage} m, {chatoperator} o " .
			"WHERE m.agentId = o.operatorid " .
				"AND (m.dtmcreated - :start) > 24*60*60 " .
				// Calculate statistics only for messages that older one day
				"AND (:today - m.dtmcreated) > 24*60*60 " .
			"GROUP BY date " .
			"ORDER BY date",
			array(
				':kind_agent' => Thread::KIND_AGENT,
				':start' => $start,
				':today' => $today
			)
		);
	} catch(Exception $e) {
		// Something went wrong: warn and rollback transaction.
		trigger_error(
			'Operator statistics calculating faild: ' . $e->getMessage(),
			E_USER_WARNING
		);
		$db->query('ROLLBACK');

		// Set throw exceptions back
		$db->throwExeptions($db_throw_exceptions);
		return;
	}

	// Commit transaction
	$db->query('COMMIT');

	// Set throw exceptions back
	$db->throwExeptions($db_throw_exceptions);
}

/**
 * Calculate aggregated 'by page' statistics
 */
function calculate_page_statistics() {
	// Prepare database
	$db = Database::getInstance();
	$db_throw_exceptions = $db->throwExeptions(true);

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
		$today = floor(time() / (24*60*60)) * 24*60*60;

		// Calculate statistics
		$db->query(
			"INSERT INTO {visitedpagestatistics} (" .
				"date, address, visits, chats" .
			") SELECT FLOOR(p.visittime / (24*60*60)) * 24*60*60 AS date, " .
				"p.address AS address, " .
				"COUNT(DISTINCT p.pageid) AS visittimes, " .
				"COUNT(DISTINCT t.threadid) AS chattimes " .
			"FROM {visitedpage} p " .
				"LEFT OUTER JOIN {chatthread} t ON (" .
					"p.address = t.referer " .
					"AND DATE(FROM_UNIXTIME(p.visittime)) = " .
						"DATE(FROM_UNIXTIME(t.dtmcreated))) " .
			"WHERE p.calculated = 0 " .
				"AND (p.visittime - :start) > 24*60*60 " .
				"AND (:today - p.visittime) > 24*60*60 " .
			"GROUP BY date, address " .
			"ORDER BY date",
			array(
				':start' => $start,
				':today' => $today
			)
		);

		// Mark all visited pages as 'calculated'
		$db->query(
			"UPDATE {visitedpage} SET calculated = 1 " .
			"WHERE (:today - visittime) > 24*60*60 " .
				"AND calculated = 0",
			array(
				':today' => $today
			)
		);

		// Remove old tracks from the system
		track_remove_old_tracks();
	} catch(Exception $e) {
		// Something went wrong: warn and rollback transaction.
		trigger_error(
			'Page statistics calculating faild: ' . $e->getMessage(),
			E_USER_WARNING
		);
		$db->query('ROLLBACK');

		// Set throw exceptions back
		$db->throwExeptions($db_throw_exceptions);
		return;
	}

	// Commit transaction
	$db->query('COMMIT');

	// Set throw exceptions back
	$db->throwExeptions($db_throw_exceptions);
}

/**
 * Add info from $additional_info to $stat_info using value of 'date' item as
 * a key.
 *
 * @param array $stat_info Statistics info
 * @param array $additional_info Data that must be added to statistics info
 * @return array Extended statistics info
 */
function extend_statistics_info($stat_info, $additional_info) {
	$result = $stat_info;
	foreach($additional_info as $row) {
		$date = $row['date'];
		if (empty($result[$date])) {
			$result[$date] = array();
		}
		$result[$date] += $row;
	}
	return $result;
}

?>