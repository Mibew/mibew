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

namespace Mibew\Controller;

use Mibew\Maintenance\CronWorker;
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
     * Triggers {@link \Mibew\EventDispatcher\Events::CRON_RUN} event.
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

        // Do the job.
        $worker = new CronWorker($this->getCache());
        $success = $worker->run();

        // Determine use or not quiet mode
        $quiet = $request->query->has('q');
        if (!$quiet) {
            if ($success) {
                // Everything is fine.
                return 'All cron jobs done.';
            }

            // Prepare error message for system's error log.
            $error_message = "Cron job failed. Here are the errors:\n";
            foreach ($worker->getErrors() as $error) {
                $error_message .= '    ' . $error . "\n";
            }
            trigger_error($error_message, E_USER_WARNING);

            // Let the client know about the problem.
            return 'Cron job failed.';
        }
    }
}
