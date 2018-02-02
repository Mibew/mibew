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

function verify_param($name, $reg_exp, $default = null)
{
    if (isset($_GET[$name]) && is_scalar($_GET[$name])) {
        $val = $_GET[$name];
        if (preg_match($reg_exp, $val)) {
            return $val;
        }
    } elseif (isset($_POST[$name]) && is_scalar($_POST[$name])) {
        $val = $_POST[$name];
        if (preg_match($reg_exp, $val)) {
            return $val;
        }
    } else {
        if (isset($default)) {
            return $default;
        }
    }
    echo "<html><head></head><body>Wrong parameter used or absent: " . $name . "</body></html>";
    exit;
}
