<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2016 the original author or authors.
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
 * Represents button-related actions
 */
class OpStatusController extends AbstractController
{
    /**
     * Returns content of the chat button.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $group_id = $request->query->get('group', '');
        if (!preg_match("/^\d{1,8}$/", $group_id)) {
            $group_id = false;
        }
        if ($group_id) {
            if (Settings::get('enablegroups') == '1') {
                $group = group_by_id($group_id);
                if (!$group) {
                    $group_id = false;
                }
            } else {
                $group_id = false;
            }
        }

        // Get image file content
        $op_status = has_online_operators($group_id) ? true : false;
        
        return $op_status;
    }
}
