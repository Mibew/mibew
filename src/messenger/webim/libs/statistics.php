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
		$db->query(
			"INSERT INTO {chatthreadstatistics} ( " .
				"date, threads, operatormessages, usermessages, " .
				"averagewaitingtime, averagechattime " .
			") SELECT (FLOOR(t.dtmcreated / (24*60*60)) * 24*60*60) AS date, " .
				"COUNT(distinct t.threadid) AS threads, " .
				"SUM(m.ikind = :kind_agent) AS operators, " .
				"SUM(m.ikind = :kind_user) AS users, " .
				"ROUND(AVG(t.dtmchatstarted-t.dtmcreated),1) as avgwaitingtime, " .
				// Prevent negative values of avgchattime field.
				// If avgchattime < 0 it becomes to zero.
				// For random value 'a' result of expression ((abs(a) + a) / 2)
				// equals to 'a' if 'a' more than zero
				// and equals to zero otherwise
				"ROUND(AVG( " .
					"ABS(tmp.lastmsgtime-t.dtmchatstarted) + " .
					"(tmp.lastmsgtime-t.dtmchatstarted) " .
				")/2,1) as avgchattime " .
			"FROM {indexedchatmessage} m, " .
				"{chatthread} t, " .
				"(SELECT i.threadid, MAX(i.dtmcreated) AS lastmsgtime " .
					"FROM {indexedchatmessage} i " .
					"WHERE (ikind = :kind_user OR ikind = :kind_agent) " .
					"GROUP BY i.threadid) tmp " .
			"WHERE m.threadid = t.threadid " .
				"AND tmp.threadid = t.threadid " .
				"AND t.dtmchatstarted <> 0 " .
				"AND (m.dtmcreated - :start) > 24*60*60 " .
				// Calculate statistics only for threads that older than one day
				"AND (:today - m.dtmcreated) > 24*60*60 " .
			"GROUP BY date " .
			"ORDER BY date",
			array(
				':kind_agent' => Thread::KIND_AGENT,
				':kind_user' => Thread::KIND_USER,
				':start' => $start,
				':today' => $today
			)
		);
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

?>