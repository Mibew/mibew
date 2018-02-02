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

function div($a, $b)
{
    return ($a - ($a % $b)) / $b;
}

/**
 * Checks if the system is under maintenance and gets maintenance mode name.
 *
 * @return boolean|string Name of maintenace mode or boolean false if the system
 *   is not in maintenance mode.
 */
function get_maintenance_mode()
{
    if (!defined('MAINTENANCE_MODE')) {
        return false;
    }

    return MAINTENANCE_MODE;
}
