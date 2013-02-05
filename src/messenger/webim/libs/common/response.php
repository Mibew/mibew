<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

require_once(dirname(__FILE__) . '/locale.php');

function get_popup($href, $jshref, $message, $title, $wndName, $options)
{
	if (!$jshref) {
		$jshref = "'$href'";
	}
	return "<a href=\"$href\" target=\"_blank\" " . ($title ? "title=\"$title\" " : "") . "onclick=\"if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 &amp;&amp; window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open($jshref, '$wndName', '$options');this.newWindow.focus();this.newWindow.opener=window;return false;\">$message</a>";
}

function get_image($href, $width, $height)
{
	if ($width != 0 && $height != 0)
		return "<img src=\"$href\" border=\"0\" width=\"$width\" height=\"$height\" alt=\"\"/>";
	return "<img src=\"$href\" border=\"0\" alt=\"\"/>";
}

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
	$charset = getstring("output_charset");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: text/html" . (isset($charset) ? "; charset=" . $charset : ""));
}

function start_js_output(){
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: application/javascript; charset=utf-8");
	header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
}

function topage($text)
{
	global $webim_encoding;
	return myiconv($webim_encoding, getoutputenc(), $text);
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
function get_additional_css($page_name) {
	// Prepare event arguments array
	$args = array(
		'page' => $page_name,
		'css' => array()
	);

	// Trigger event
	$dispatcher = EventDispatcher::getInstance();
	$dispatcher->triggerEvent('pageAddCSS', $args);

	// Build resulting css list
	$result = array();
	foreach($args['css'] as $css) {
		$result[] = '<link rel="stylesheet" type="text/css" href="'.$css.'">';
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
function get_additional_js($page_name) {
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
	foreach($args['js'] as $js) {
		$result[] = '<script type="text/javascript" src="'.$js.'"></script>';
	}

	return implode("\n", $result);
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
function get_js_plugin_options($page_name) {
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

function no_field($key)
{
	return getlocal2("errors.required", array(getlocal($key)));
}

function failed_uploading_file($filename, $key)
{
	return getlocal2("errors.failed.uploading.file",
		array($filename, getlocal($key)));
}

function wrong_field($key)
{
	return getlocal2("errors.wrong_field", array(getlocal($key)));
}

function add_params($servlet, $params)
{
	$infix = '?';
	if (strstr($servlet, $infix) !== FALSE)
		$infix = '&amp;';
	foreach ($params as $k => $v) {
		$servlet .= $infix . $k . "=" . $v;
		$infix = '&amp;';
	}
	return $servlet;
}

?>