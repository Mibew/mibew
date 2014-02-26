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

/**
 * Builds list of operator settings tabs. The keys of the resulting array are
 * tabs titles and the values are tabs URLs.
 *
 * @param int $operator_id ID of the operator whose settings page is displayed.
 * @param int $active Number of the active tab. The count starts from 0.
 * @return array Tabs list
 */
function setup_operator_settings_tabs($operator_id, $active)
{
    $tabs = array();

    if ($operator_id) {
        $tabs = array(
            getlocal("page_agent.tab.main") => ($active != 0
                ? (MIBEW_WEB_ROOT . "/operator/operator.php?op=" . $operator_id)
                : ""),
            getlocal("page_agent.tab.avatar") => ($active != 1
                ? (MIBEW_WEB_ROOT . "/operator/avatar.php?op=" . $operator_id)
                : ""),
            getlocal("page_agent.tab.groups") => ($active != 2
                ? (MIBEW_WEB_ROOT . "/operator/opgroups.php?op=" . $operator_id)
                : ""),
            getlocal("page_agent.tab.permissions") => ($active != 3
                ? (MIBEW_WEB_ROOT . "/operator/permissions.php?op=" . $operator_id)
                : ""),
        );
    }

    return $tabs;
}
