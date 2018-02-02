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

function get_popup($href, $js_href, $message, $title, $wnd_name, $options)
{
    if (!$js_href) {
        $js_href = "'$href'";
    }
    return "<a href=\"$href\" target=\"_blank\" "
        . ($title ? "title=\"$title\" " : "")
        . "onclick=\"if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 "
        . "&amp;&amp; window.event.preventDefault) window.event.preventDefault();"
        . "this.newWindow = window.open($js_href, '$wnd_name', '$options');"
        . "this.newWindow.focus();this.newWindow.opener=window;return false;\">$message</a>";
}

function no_field($key)
{
    return getlocal('Please fill "{0}".', array(getlocal($key)));
}

function failed_uploading_file($filename, $key)
{
    return getlocal('Error uploading file "{0}": {1}.', array($filename, getlocal($key)));
}

function wrong_field($key)
{
    return getlocal('Please fill "{0}" correctly.', array(getlocal($key)));
}

function add_params($servlet, $params)
{
    $infix = '?';
    if (strstr($servlet, $infix) !== false) {
        $infix = '&amp;';
    }
    foreach ($params as $k => $v) {
        $servlet .= $infix . $k . "=" . $v;
        $infix = '&amp;';
    }

    return $servlet;
}
