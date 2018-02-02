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

function get_get_param($name, $default = '')
{
    if (!isset($_GET[$name]) || !$_GET[$name]) {
        return $default;
    }
    $value = unicode_urldecode($_GET[$name]);
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }

    return $value;
}
