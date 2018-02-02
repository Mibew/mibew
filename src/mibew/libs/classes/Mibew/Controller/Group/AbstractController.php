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

namespace Mibew\Controller\Group;

use Mibew\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a set of utility functions.
 */
abstract class AbstractController extends BaseController
{
    /**
     * Builds list of the group tabs.
     *
     * @param Request $request Current request.
     * @return array Tabs list. The keys of the array are tabs titles and the
     *   values are tabs URLs.
     */
    protected function buildTabs(Request $request)
    {
        $tabs = array();
        $route = $request->attributes->get('_route');
        $group_id = $request->attributes->get('group_id', false);
        $args = array('group_id' => $group_id);

        if ($group_id) {
            $tabs[getlocal('General')] = ($route == 'group_members')
                ? $this->generateUrl('group_edit', $args)
                : '';

            $tabs[getlocal('Members')] = ($route != 'group_members')
                ? $this->generateUrl('group_members', $args)
                : '';
        }

        return $tabs;
    }
}
