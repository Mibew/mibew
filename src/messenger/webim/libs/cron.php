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
 * Index messages for closed threads
 *
 * History and serarch works only for indexed messages.
 *
 * Trigger 'pluginMessageIndex' event. Plugins can use this event to
 * index their own messages.
 *
 * Event listener receives as argument an associative array with following keys:
 *  - 'message': associative array with message data;
 *  - 'result': array of indexed message data. Default value is boolean false.
 *
 * The 'message' element contains following keys:
 *  - 'plugin': string, name of the plugin which sent the message;
 *  - 'data': array, data sent by the plugin.
 *
 * Plugin can use the 'result' element to return indexed message data. By default it
 * equals to boolean false. To index message plugin should set this element to
 * an associative array with following keys:
 *  - 'kind': int, indexed message kind. It must be one of the core messages
 *    kinds, not Thread::KIND_PLUGIN.
 *  - 'message': string, text of indexed message.
 *  - 'sender_name': string, name of person who sent the message. This field is
 *    arbitrary and do not use for some messages kinds.
 *  - 'operator_id': int, ID of the operator who send the message. This field is
 *    arbitrary and do not use for some messages kinds.
 *
 * If the 'result' element equals to boolean false message will be skipped.
 *
 * Example of event listener is listed below:
 * <code>
 *   public function pluginMessageIndexListener(&$args) {
 *     // Create shortcut for stored data
 *     $data = $args['message']['data'];
 *     // Check if message was sent by current plugin
 *     if ($args['message']['plugin'] == 'example') {
 *       $args['result'] = array(
 *         // Plugin should set one of the core message kinds to indexed message
 *         'kind' => Thread::KIND_INFO,
 *         // Plugin should set arbitrary text for indexed message
 *         'message' => $data['title'] . ': ' . $data['body']
 *       );
 *     }
 *   }
 * </code>
 */
function cron_index_messages() {
	$db = Database::getInstance();
	$db->throwExeptions(true);

	try {
		// Start transaction
		$db->query('START TRANSACTION');

		// Select messages from closed threads
		$messages = $db->query(
			"SELECT {chatmessage}.* FROM {chatmessage}, {chatthread} " .
				"WHERE {chatmessage}.threadid = {chatthread}.threadid AND " .
				"({chatthread}.istate = :closed OR {chatthread}.istate = :left)",
			array(
				':closed' => Thread::STATE_CLOSED,
				':left' => Thread::STATE_LEFT
			),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);

		// Prevent race condition. Remove only moved to {indexedchatmessage}
		// messages. Get last loaded message ID for that.
		$last_id = 0;
		$dispatcher = EventDispatcher::getInstance();
		foreach($messages as $key => $message) {
			// Find last message ID
			if ($message['messageid'] > $last_id) {
				$last_id = $message['messageid'];
			}

			// Leave core messages kind as is
			if ($message['ikind'] != Thread::KIND_PLUGIN) {
				continue;
			}

			// Provide an ability for plugins to index own messages
			$event_args = array(
				'message' => unserialize($message['tmessage']),
				'result' => false
			);
			$dispatcher->triggerEvent('pluginMessageIndex', $event_args);

			// Check if message was processed by a plugin correctly
			$update_message = true;
			if (empty($event_args['result']['message'])) {
				$update_message = false;
			}

			// Check if kind set and correct
			if (empty($event_args['result']['kind'])
				|| $event_args['result']['kind'] == Thread::KIND_PLUGIN) {
				$update_message = false;
			}

			// Check if message should be updated
			if (! $update_message) {
				unset($messages[$key]);
				continue;
			}

			// Update message
			$messages[$key]['ikind'] = $event_args['result']['kind'];
			$messages[$key]['tmessage'] = $event_args['result']['message'];

			if (array_key_exists('sender_name', $event_args['result'])) {
				$messages[$key]['tname'] = $event_args['result']['sender_name'];
			}

			if (array_key_exists('operator_id', $event_args['result'])) {
				$messages[$key]['agentId'] = $event_args['result']['operator_id'];
			}
		}

		// Check is there some messages that should be saved
		if (count($messages) != 0) {
			// Reindex messages array
			$messages = array_values($messages);
			// Prepare SQL query template
			$message_fields = array_keys($messages[0]);
			$placeholders = '(' .
				implode(', ', array_fill(0, count($message_fields), '?')) . ')';
			$sql_template = 'INSERT INTO {indexedchatmessage} (' .
				implode(', ', $message_fields) . ') VALUES ';

			// Insert indexed messages into database by $block_size messages per
			// sql query
			$block_size = 20;
			$iteration_count = ceil(count($messages) / $block_size);
			for($i = 0; $i < $iteration_count; $i++) {
				// Get messages block
				$messages_block = array_slice(
					$messages,
					$i * $block_size,
					$block_size
				);

				// Count of $messages_block can be less than $block_size for
				// the last block of messages.
				$real_block_size = count($messages_block);

				// Build array of inserted values
				$fields_to_insert = array();
				foreach($messages_block as $message) {
					foreach($message_fields as $field_name) {
						$fields_to_insert[] = $message[$field_name];
					}
				}

				// Build query
				$sql = $sql_template . implode(
					', ',
					array_fill(0, $real_block_size, $placeholders)
				);

				// Run query
				$db->query($sql, $fields_to_insert);
			}
		}

		// Check is there some processed messages that should be deleted
		if ($last_id != 0) {
			// Delete indexed messages
			$db->query(
				'DELETE FROM {chatmessage} where messageid <= :last_id',
				array(':last_id' => $last_id)
			);
		}
	} catch (Exception $e) {
		// Something went wrong: warn and rollback transaction.
		trigger_error(
			'Messages indexing faild: ' . $e->getMessage(),
			E_USER_WARNING
		);
		$db->query('ROLLBACK');
		return;
	}

	// Commit transaction
	$db->query('COMMIT');
}

/**
 * Calculate aggregated 'by thread' and 'by operator' statistics
 */
function cron_calculate_statistics() {
	// Prepare database
	$db = Database::getInstance();
	$db->throwExeptions(true);

	try {
		// Start transaction
		$db->query('START TRANSACTION');

		// Build 'by thread' statistics
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

		// Calculate 'by thread' statistics
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

		// Build 'by operator' statistics
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

		// Caclculate 'by operator' statistics
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

		// Build 'by page' statistics
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
			'Statistics calculating faild: ' . $e->getMessage(),
			E_USER_WARNING
		);
		$db->query('ROLLBACK');
		return;
	}

	// Commit transaction
	$db->query('COMMIT');
}

/**
 * Generates cron URI
 *
 * @global string $webimroot Path of the mibew instalation from server root.
 * It defined in libs/config.php
 * @param string $cron_key Cron security key
 * @return string Cron URI
 */
function cron_get_uri($cron_key) {
	global $webimroot;

	$path = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
		? 'http://'
		: 'https://';
	$path .= $_SERVER['SERVER_NAME'] . $webimroot . '/cron.php';
	$path .= empty($cron_key)
		? ''
		: '?cron_key='.$cron_key;
	return $path;
}

?>