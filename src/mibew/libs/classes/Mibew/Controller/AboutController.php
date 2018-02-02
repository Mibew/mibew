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

use Mibew\Maintenance\AvailableUpdate;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all about page relates actions.
 */
class AboutController extends AbstractController
{
    /**
     * Generates "about" page.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $operator = $this->getOperator();
        $page = array_merge(
            array(
                'showSystemInfo' => is_capable(CAN_ADMINISTRATE, $operator),
                'localizations' => get_available_locales(),
                'phpVersion' => phpversion(),
                'extensions' => $this->getExtensionsInfo(),
                'version' => MIBEW_VERSION,
                'title' => getlocal('About'),
                'menuid' => 'about',
                'availableUpdates' => $this->getAvailableUpdates(),
            ),
            prepare_menu($operator)
        );

        return $this->render('about', $page);
    }

    /**
     * Builds info about required extensions.
     *
     * @return array Associative array of extensions info. Its keys are
     * extensions names and the values are associative arrays with the following
     * keys:
     *  - "loaded": boolean, indicates it the extension was loaded or not.
     *  - "version": string, extension version or boolean false if the version
     *    cannot be obtained.
     */
    protected function getExtensionsInfo()
    {
        $required_extensions = array('PDO', 'pdo_mysql', 'gd', 'curl', 'mbstring');
        $info = array();
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $info[$ext] = array(
                    'loaded' => false,
                    'version' => false,
                );
            } else {
                $info[$ext] = array(
                    'loaded' => true,
                    'version' => phpversion($ext),
                );
            }
        }

        return $info;
    }

    /**
     * Builds list of available updates to display in the template.
     *
     * @return array List of updates data. Each item of the list is associative
     * array with the following keys:
     *   - "title": string, title of the update.
     *   - "version": string, the latest available version.
     *   - "url": string, URL of the page the updated version can be downloaded
     *     from.
     *   - "description": string, description of the update.
     */
    protected function getAvailableUpdates()
    {
        $updates = AvailableUpdate::all();
        if (!$updates) {
            return array();
        }

        $data = array();
        foreach ($updates as $update) {
            $title = ($update->target == 'core')
                ? 'Mibew'
                : getlocal('{0} plugin', array($update->target));

            $data[] = array(
                'title' => $title,
                'version' => $update->version,
                'url' => $update->url,
                'description' => $update->description,
            );
        }

        return $data;
    }
}
