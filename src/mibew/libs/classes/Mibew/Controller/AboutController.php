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
        $page = array_merge(
            array(
                'title' => getlocal('About'),
                'menuid' => 'about',
            ),
            prepare_menu($this->getOperator())
        );

        return $this->render('about', $page);
    }
}
