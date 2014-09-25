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

use Mibew\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

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

/**
 * Load additional CSS files, required by plugins, and build HTML code to
 * include them
 *
 * Triggers 'pageAddCSS' and pass listeners associative array with
 * following keys:
 *  - 'request': {@link \Symfony\Component\HttpFoundation\Request}, a request
 *    instance. CSS files will be attached to the requested page.
 *  - 'css': array, with CSS files paths. Modify this array to add or remove
 *    additional CSS files.
 *
 * @param Request $request A Request instance.
 * @return string HTML block of 'link' tags
 */
function get_additional_css(Request $request)
{
    // Prepare event arguments array
    $args = array(
        'request' => $request,
        'css' => array(),
    );

    // Trigger event
    $dispatcher = EventDispatcher::getInstance();
    $dispatcher->triggerEvent('pageAddCSS', $args);

    // Build resulting css list
    $result = array();
    foreach ($args['css'] as $css) {
        $result[] = '<link rel="stylesheet" type="text/css" href="' . $css . '">';
    }

    return implode("\n", $result);
}

/**
 * Load additional JavaScript files, required by plugins, and build HTML code
 * to include them
 *
 * Triggers 'pageAddJS' and pass listeners associative array with
 * following keys:
 *  - 'request': {@link \Symfony\Component\HttpFoundation\Request}, a request
 *    instance. JavaScript files will be attached to the requested page.
 *  - 'js': array, with JavaScript files paths. Modify this array to add or
 *    remove additional JavaScript files.
 *
 * @param Request $request A Request instance.
 * @return string HTML block of 'script' tags
 */
function get_additional_js(Request $request)
{
    // Prepare event arguments array
    $args = array(
        'request' => $request,
        'js' => array()
    );

    // Trigger event
    $dispatcher = EventDispatcher::getInstance();
    $dispatcher->triggerEvent('pageAddJS', $args);

    // Build resulting css list
    $result = array();
    foreach ($args['js'] as $js) {
        $result[] = '<script type="text/javascript" src="' . $js . '"></script>';
    }

    return implode("\n", $result);
}

/**
 * Build Javascript code that contains initializing options for JavaScript
 * plugins
 *
 * Triggers 'pageAddJSPluginOptions' and pass listeners associative array with
 * following keys:
 *  - 'request': {@link \Symfony\Component\HttpFoundation\Request}, a request
 *    instance. Plugins will work at the requested page.
 *  - 'plugins': associative array, whose keys are plugins names and values are
 *    plugins options. Modify this array to add or change plugins options
 *
 * @param Request $request A Request instance.
 * @return string JavaScript options block
 */
function get_js_plugin_options(Request $request)
{
    // Prepare event arguments array
    $args = array(
        'request' => $request,
        'plugins' => array()
    );

    // Trigger event
    $dispatcher = EventDispatcher::getInstance();
    $dispatcher->triggerEvent('pageAddJSPluginOptions', $args);

    // Return encoded options
    return json_encode($args['plugins']);
}

/**
 * Get additional plugins data for specified page
 *
 * @param Request $request A Request instance.
 * @return array Associative array of plugins data. It contains following keys:
 *    - 'additional_css': contains results of the 'get_additional_css function
 *    - 'additional_js': contains results of the 'get_additional_js' function
 *    - 'js_plugin_options': contains results of the 'get_js_plugin_options'
 *      function
 */
function get_plugins_data(Request $request)
{
    return array(
        'additional_css' => get_additional_css($request),
        'additional_js' => get_additional_js($request),
        'js_plugin_options' => get_js_plugin_options($request)
    );
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
