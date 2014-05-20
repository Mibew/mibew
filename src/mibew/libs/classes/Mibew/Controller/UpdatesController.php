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

namespace Mibew\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Build updates info page.
 */
class UpdatesController extends AbstractController
{
    /**
     * Generate a page with updates list.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $operator = $request->attributes->get('_operator');
        $default_extensions = array('mysql', 'gd', 'iconv');

        $page = array(
            'localizations' => get_available_locales(),
            'phpVersion' => phpversion(),
            'version' => MIBEW_VERSION,
            'title' => getlocal("updates.title"),
            'menuid' => "updates",
            'errors' => array(),
        );

        foreach ($default_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $page['phpVersion'] .= " $ext/absent";
            } else {
                $ver = phpversion($ext);
                $page['phpVersion'] .= $ver ? " $ext/$ver" : " $ext";
            }
        }

        $page = array_merge($page, prepare_menu($operator));

        return $this->render('updates', $page);
    }
}
