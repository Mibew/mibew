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

// Import namespaces and classes of the core
use Mibew\Settings;

/* ajax server actions use utf-8 */
function get_raw_param($name)
{
    if (isset($_POST[$name])) {
        $value = $_POST[$name];
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }

        return $value;
    }
    die("no " . $name . " parameter");
}
/* form processors use current Output encoding */

function get_param($name)
{
    if (isset($_POST[$name])) {
        $value = $_POST[$name];
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }

        return $value;
    }
    die("no " . $name . " parameter");
}

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

function get_app_location($show_host, $is_secure)
{
    if ($show_host) {
        return ($is_secure ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . MIBEW_WEB_ROOT;
    } else {
        return MIBEW_WEB_ROOT;
    }
}

function is_secure_request()
{
    return (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')
        || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
        || (isset($_SERVER["HTTP_HTTPS"]) && $_SERVER["HTTP_HTTPS"] == "on");
}

/**
 * Returns name of the current operator pages style
 *
 * @return string
 */
function get_page_style()
{
    return Settings::get('page_style');
}
