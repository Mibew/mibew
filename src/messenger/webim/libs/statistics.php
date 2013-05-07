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

		// Reset statistics for the last day, because cron can be ran many
		// times in a day.
		$result = $db->query(
			"DELETE FROM {chatthreadstatistics} WHERE date = :start",
			array(':start' => $start)
		);

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
				"AND m.dtmcreated > :start " .
			"GROUP BY date " .
			"ORDER BY date",
			array(
				':kind_agent' => Thread::KIND_AGENT,
				':kind_user' => Thread::KIND_USER,
				':start' => $start
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

		// Reset statistics for the last day, because cron can be ran many
		// times in a day.
		$result = $db->query(
			"DELETE FROM {chatoperatorstatistics} WHERE date = :start",
			array(':start' => $start)
		);

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
				"AND m.dtmcreated > :start " .
			"GROUP BY date " .
			"ORDER BY date",
			array(
				':kind_agent' => Thread::KIND_AGENT,
				':start' => $start
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

		$visited_pages = $db->query(
			"SELECT FLOOR(p.visittime / (24*60*60)) * 24*60*60 AS date, " .
				"p.address AS address, " .
				// 'visittimes' is not calculated pages count. It means that
				// 'visittimes' is count of NEW visited pages, not total count.
				"COUNT(DISTINCT p.pageid) AS visittimes, " .
				// 'chattimes' is total count of threads related with a page
				// address, not a visited page row. It means that 'chattimes' is
				// TOTAL chats count from this page, not only new.
				"COUNT(DISTINCT t.threadid) AS chattimes " .
			"FROM {visitedpage} p " .
				"LEFT OUTER JOIN {chatthread} t ON (" .
					"p.address = t.referer " .
					"AND DATE(FROM_UNIXTIME(p.visittime)) = " .
						"DATE(FROM_UNIXTIME(t.dtmcreated))) " .
			"WHERE p.calculated = 0 " .
			"GROUP BY date, address " .
			"ORDER BY date",
			array(),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		foreach($visited_pages as $visited_page) {
			// Check is there statistics for current visited page in database.
			$count_result = $db->query(
				"SELECT COUNT(*) AS count " .
				"FROM {visitedpagestatistics} " .
				"WHERE date = :date AND address = :address",
				array(
					':date' => $visited_page['date'],
					':address' => $visited_page['address']
				),
				array('return_rows' => Database::RETURN_ONE_ROW)
			);

			if (! empty($count_result['count'])) {
				// Stat already in database. Update it.
				$db->query(
					"UPDATE {visitedpagestatistics} SET " .
						"visits = visits + :visits, " .
						// Do not add chat because of it is total count of chats
						// related with this page.
						// TODO: Think about old threads removing. In current
						// configuration it can cause problems with wrong
						// 'by page' statistics.
						"chats = :chats " .
					"WHERE date = :date " .
						"AND address = :address " .
					"LIMIT 1",
					array(
						':date' => $visited_page['date'],
						':address' => $visited_page['address'],
						':visits' => $visited_page['visittimes'],
						':chats' => $visited_page['chattimes']
					)
				);
			} else {
				// Create stat row in database.
				$db->query(
					"INSERT INTO {visitedpagestatistics} (" .
						"date, address, visits, chats" .
					") VALUES ( " .
						":date, :address, :visits, :chats" .
					")",
					array(
						':date' => $visited_page['date'],
						':address' => $visited_page['address'],
						':visits' => $visited_page['visittimes'],
						':chats' => $visited_page['chattimes']
					)
				);
			}
		}

		// Mark all visited pages as 'calculated'
		$db->query("UPDATE {visitedpage} SET calculated = 1");

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