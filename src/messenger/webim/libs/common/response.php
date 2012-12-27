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
 * Load additional CSS files, required by plugins, and build HTML code to include them
 *
 * @param string $page_name CSS files load to this page
 * @return string HTML block of 'link' tags
 */
function get_additional_css($page_name) {
	$method = $page_name . 'AddCss';
	$plugins = PluginManager::getAllPlugins();
	$result = array();
	// Check all plugins
	foreach ($plugins as $plugin) {
		if (is_callable(array($plugin, $method))) {
			// Try to invoke '<$page_name>AddCss' method
			$css_list = $plugin->$method();
			foreach ($css_list as $css) {
				// Add script tag for each javascript file
				$result[] = '<link rel="stylesheet" type="text/css" href="' . $css . '">';
			}
		}
	}
	return implode("\n", $result);
}

/**
 * Load additional JavaScript files, required by plugins, and build HTML code to include them
 *
 * @param string $page_name JavaScript files load to this page
 * @return string HTML block of 'script' tags
 */
function get_additional_js($page_name) {
	$method = $page_name . 'AddJs';
	$plugins = PluginManager::getAllPlugins();
	$result = array();
	// Check all plugins
	foreach ($plugins as $plugin) {
		if (is_callable(array($plugin, $method))) {
			// Try to invoke '<$page_name>AddJs' method
			$js_list = $plugin->$method();
			foreach ($js_list as $js) {
				// Add script tag for each javascript file
				$result[] = '<script type="text/javascript" src="' . $js . '"></script>';
			}
		}
	}
	return implode("\n", $result);
}

/**
 * Build Javascript code that contains initializing options for JavaScript plugins
 *
 * @param string $page_name Plugins initialize at this page
 * @return string JavaScript options block
 */
function get_js_plugin_options($page_name) {
	$method = $page_name . 'AddJsPluginOptions';
	$plugins = PluginManager::getAllPlugins();
	$result = array();
	// Check all plugins
	foreach ($plugins as $plugin) {
		if (is_callable(array($plugin, $method))) {
			// Try to invoke '<$page_name>AddJsPluginOptions' method
			$js_plugins = $plugin->$method();
			foreach ($js_plugins as $js_plugin => $js_options) {
				// Add plugin's initialization options code
				if (empty($result[$js_plugin])) {
					$result[$js_plugin] = array();
				}
				$result[$js_plugin] = array_merge($result[$js_plugin], $js_options);
			}
		}
	}
	return json_encode($result);
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