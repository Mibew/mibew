<?php
/*
 * This file is a part of Mibew Messenger.
 *
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

namespace Mibew\Controller;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\Settings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions which are related with cron.
 */
class CronController extends AbstractController
{
    /**
     * Runs the cron.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function runAction(Request $request)
    {
        $cron_key = $request->query->get('cron_key', '');

        // Check cron security key
        if ($cron_key != Settings::get('cron_key')) {
            // Return an empty response
            return '';
        }

        // Determine use or not quiet mode
        $quiet = $request->query->has('q');

        set_time_limit(0);

        // Remove stale cached items
        $this->getCache()->purge();

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
            return 'All cron jobs done.';
        }
    }
}
