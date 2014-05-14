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
use Mibew\EventDispatcher;

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

function get_image($href, $width, $height)
{
    if ($width != 0 && $height != 0) {
        return "<img src=\"$href\" border=\"0\" width=\"$width\" height=\"$height\" alt=\"\"/>";
    }

    return "<img src=\"$href\" border=\"0\" alt=\"\"/>";
}

/**
 * Sends headers that are needed for XML responses.
 *
 * @deprecated
 */
function start_xml_output()
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-type: text/xml; charset=utf-8");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
}

function start_html_output()
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-type: text/html; charset=utf-8");
}

/**
 * Sends headers that are needed for JS responses.
 *
 * @deprecated
 */
function start_js_output()
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-type: application/javascript; charset=utf-8");
    header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
}

/**
 * Load additional CSS files, required by plugins, and build HTML code to
 * include them
 *
 * Triggers 'pageAddCSS' and pass listeners associative array with
 * following keys:
 *  - 'page': string, name of page to which CSS files will be attached.
 *  - 'js': array, with CSS files paths. Modify this array to add or remove
 *    additional CSS files.
 *
 * @param string $page_name CSS files load to this page
 * @return string HTML block of 'link' tags
 */
function get_additional_css($page_name)
{
    // Prepare event arguments array
    $args = array(
        'page' => $page_name,
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
 *  - 'page': string, name of page to which JavaScript files will be attached.
 *  - 'js': array, with JavaScript files paths. Modify this array to add or
 *    remove additional JavaScript files.
 *
 * @param string $page_name JavaScript files load to this page
 * @return string HTML block of 'script' tags
 */
function get_additional_js($page_name)
{
    // Prepare event arguments array
    $args = array(
        'page' => $page_name,
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
 * Add additional localized strings for JavaScript application
 *
 * Triggers 'pageAddLocalizedStrings' and pass listeners associative array with
 * following keys:
 *  - 'page': string, name of page to which localized strings will be added.
 *  - 'localized_strings': associative array with localized strings.
 *
 * @param string $page_name Localized strings add to this page
 * @return string JSON encoded localized strings
 */
function get_additional_localized_strings($page_name)
{
    // Prepare event arguments array
    $args = array(
        'page' => $page_name,
        'localized_strings' => array(),
    );

    // Trigger event
    $dispatcher = EventDispatcher::getInstance();
    $dispatcher->triggerEvent('pageAddLocalizedStrings', $args);

    // Build result
    $result = array();
    if (!empty($args['localized_strings']) && is_array($args['localized_strings'])) {
        $result = $args['localized_strings'];
    }

    return json_encode($result);
}

/**
 * Build Javascript code that contains initializing options for JavaScript
 * plugins
 *
 * Triggers 'pageAddJSPluginOptions' and pass listeners associative array with
 * following keys:
 *  - 'page': string, name of page at which plugins will work.
 *  - 'plugins': associative array, whose keys are plugins names and values are
 *    plugins options. Modify this array to add or change plugins options
 *
 * @param string $page_name Plugins initialize at this page
 * @return string JavaScript options block
 */
function get_js_plugin_options($page_name)
{
    // Prepare event arguments array
    $args = array(
        'page' => $page_name,
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
 * @param string $page_name Plugins initialize at this page
 * @return array Associative array of plugins data. It contains following keys:
 *    - 'additional_css': contains results of the 'get_additional_css function
 *    - 'additional_js': contains results of the 'get_additional_js' function
 *    - 'additional_localized_strings': contains results of the
 *      'get_additional_localized_strings' function
 *    - 'js_plugin_options': contains results of the 'get_js_plugin_options'
 *      function
 */
function get_plugins_data($page_name)
{
    return array(
        'additional_css' => get_additional_css($page_name),
        'additional_js' => get_additional_js($page_name),
        'additional_localized_strings' => get_additional_localized_strings($page_name),
        'js_plugin_options' => get_js_plugin_options($page_name)
    );
}

function no_field($key)
{
    return getlocal2("errors.required", array(getlocal($key)));
}

function failed_uploading_file($filename, $key)
{
    return getlocal2("errors.failed.uploading.file", array($filename, getlocal($key)));
}

function wrong_field($key)
{
    return getlocal2("errors.wrong_field", array(getlocal($key)));
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
