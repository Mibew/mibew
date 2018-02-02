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

use Mibew\Settings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Generates content for home pages.
 */
class HomeController extends AbstractController
{
    /**
     * Redirects client's browser to operator's home page.
     *
     * @param Request $request Incoming request.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectAction(Request $request)
    {
        return $this->redirect($this->generateUrl('home_operator'));
    }

    /**
     * Renders operator's home page.
     *
     * @param Request $request Incoming request
     * @return string Rendered page content.
     */
    public function dashboardAction(Request $request)
    {
        $operator = $this->getOperator();

        $page = array(
            'version' => MIBEW_VERSION,
            'localeLinks' => get_locale_links(),
            'needUpdate' => version_compare(Settings::get('dbversion'), MIBEW_VERSION, '<'),
            'profilePage' => $this->generateUrl('operator_edit', array('operator_id' => $operator['operatorid'])),
            'warnOffline' => true,
            'title' => getlocal('Home'),
            'menuid' => 'main',
        );

        $page = array_merge($page, prepare_menu($operator));

        return $this->render('index', $page);
    }
}
