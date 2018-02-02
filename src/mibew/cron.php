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

// Initialize libraries
require_once(dirname(__FILE__) . '/libs/init.php');

use Mibew\Cache\CacheFactory;
use Mibew\Maintenance\CronWorker;
use Mibew\Plugin\PluginManager;

$configs = load_system_configs();

// Prepare the cache. It is initialized in the same way as in index.php
$cache_factory = new CacheFactory($configs['cache']);
// For now directory for cache files cannot be changed via the configs file.
$cache_factory->setOption('path', MIBEW_FS_ROOT . '/cache/stash');

// Run plugins
if (get_maintenance_mode() === false) {
    $plugin_manager = PluginManager::getInstance();
    $plugin_manager->setCache($cache_factory->getCache());
    $plugin_manager->loadPlugins($configs['plugins']);
}

// Do the job.
$worker = new CronWorker($cache_factory->getCache());
$success = $worker->run();

if ($success) {
    echo("All cron jobs done\n");
} else {
    echo("Cron job failed. Here are the errors:\n");
    foreach ($worker->getErrors() as $error) {
        echo('    ' . $error . "\n");
    }
}
