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
 * Read and parse configuration ini file
 *
 * @param string $file Path to Configuration file
 * @return boolean|array Array of configurations or boolean false if file can
 * not be read.
 */
function read_config_file($file) {
	if (! is_readable($file)) {
		return false;
	}
	return parse_ini_file($file, true);
}

/**
 * Load configuration array for core style
 *
 * @return array Configuration array
 */
function get_core_style_config() {
	// Get root dir of mibew messanger
	$base_path = realpath(dirname(__FILE__) . '/../../');

	// Load config
	$config = read_config_file($base_path.'/view/config.ini');

	// Set default values
	$config = ($config === false) ? array() : $config;
	$config += array(
		'history' => array(
			'window_params' => ''
		),
		'users' => array(
			'thread_tag' => 'div',
			'visitor_tag' => 'div'
		),
		'tracked' => array(
			'user_window_params' => '',
			'visitor_window_params' => ''
		),
		'invitation' => array(
			'window_params' => ''
		),
		'ban' => array(
			'window_params' => ''
		)
	);

	return $config;
}

/**
 * Load configuration array for dialogs style
 *
 * @param string $style Style name
 * @return array Configuration array
 */
function get_dialogs_style_config($style) {
	// Get root dir of mibew messanger
	$base_path = realpath(dirname(__FILE__) . '/../../');

	// Load config
	$config = read_config_file($base_path.'/styles/dialogs/'.$style.'/config.ini');

	// Set default values
	$config = ($config === false) ? array() : $config;
	$config += array(
		'chat' => array(
			'window_params' => ''
		),
		'mail' => array(
			'window_params' => ''
		)
	);

	return $config;
}

?>