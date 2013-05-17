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
	$db_throw_exceptions = $db->throwExeptions(true);

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
 * Generates cron URI
 *
 * @param string $cron_key Cron security key
 * @return string Cron URI
 */
function cron_get_uri($cron_key) {
	$path = get_app_location(true, is_secure_request()) . '/cron.php';
	$path .= empty($cron_key)
		? ''
		: '?cron_key='.$cron_key;
	return $path;
}

?>