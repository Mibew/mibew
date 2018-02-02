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

use Mibew\Maintenance\UpdateChecker;
use Mibew\Maintenance\Updater;
use Mibew\Settings;
use Mibew\Style\PageStyle;
use Symfony\Component\HttpFoundation\Request;

/**
 * Process all pages related with update.
 */
class UpdateController extends AbstractController
{
    /**
     * @var Updater|null
     */
    protected $updater = null;

    /**
     * @var UpdateChecker|null
     */
    protected $updateChecker = null;

    /**
     * Renders update intro page.
     *
     * @param Request $request Incoming request.
     * @return Response|string Rendered page contents or Symfony's response
     *   object.
     */
    public function indexAction(Request $request)
    {
        $parameters = array(
            'version' => MIBEW_VERSION,
            'fixedwrap' => true,
            'title' => getlocal('Update'),
        );

        return $this->render('update_intro', $parameters);
    }

    /**
     * Runs the Updater.
     *
     * @param Request $request Incoming request.
     * @return Response|string Rendered page contents or Symfony's response
     *   object.
     */
    public function runUpdateAction(Request $request)
    {
        $upd = $this->getUpdater();
        $upd->run();

        $parameters = array(
            'version' => MIBEW_VERSION,
            'fixedwrap' => true,
            'title' => getlocal('Update'),
            'done' => $this->getUpdater()->getLog(),
            'errors' => $this->getUpdater()->getErrors(),
        );

        return $this->render('update_progress', $parameters);
    }

    /**
     * Runs the Update checker.
     *
     * @param Request $request Incoming request.
     * @return Response|string Rendered page contents or Symfony's response
     *   object.
     */
    public function checkUpdatesAction(Request $request)
    {
        $checker = $this->getUpdateChecker();
        $success = $checker->run();
        if (!$success) {
            foreach ($checker->getErrors() as $error) {
                trigger_error('Update checking failed: ' . $error, E_USER_WARNING);
            }
        }

        return $this->redirect($this->generateUrl('about'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getStyle()
    {
        if (is_null($this->style)) {
            $this->style = $this->prepareStyle(new PageStyle('default'));
        }

        return $this->style;
    }

    /**
     * Returns an instance of Updater.
     *
     * @return Updater
     */
    protected function getUpdater()
    {
        if (is_null($this->updater)) {
            $this->updater = new Updater($this->getCache());
        }

        return $this->updater;
    }

    /**
     * Returns an instance of Update Checker.
     *
     * @return UpdateChecker
     */
    protected function getUpdateChecker()
    {
        if (is_null($this->updateChecker)) {
            $this->updateChecker = new UpdateChecker();
            $id = Settings::get('_instance_id');
            if ($id) {
                $this->updateChecker->setInstanceId($id);
            }
        }

        return $this->updateChecker;
    }
}
