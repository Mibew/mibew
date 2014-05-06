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
use Mibew\EventDispatcher;
use Mibew\Settings;

// Initialize libraries
require_once(dirname(__FILE__) . '/libs/init.php');

$cron_key = empty($_GET['cron_key']) ? '' : $_GET['cron_key'];

// Check cron security key
if ($cron_key != Settings::get('cron_key')) {
    die();
}

// Determine use or not quiet mode
$quiet = isset($_GET['q']);

set_time_limit(0);

// Run cron jobs of the core
calculate_thread_statistics();
calculate_operator_statistics();
calculate_page_statistics();

// Trigger cron event
$dispatcher = EventDispatcher::getInstance();
$dispatcher->triggerEvent('cronRun');

// Update time of last cron run
Settings::set('_last_cron_run', time());
Settings::update();

if (!$quiet) {
    // TODO: May be localize it
    echo('All cron jobs done.');
}
