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

$ifregexp = "/\\\${(if|ifnot):([\w\.]+)}(.*?)(\\\${else:\\2}.*?)?\\\${endif:\\2}/s";
$expand_include_path = "";
$current_style = "";
$flatten_page = array();


function check_condition($condition)
{
	global $errors, $page, $flatten_page;
	if ($condition == 'errors') {
		return isset($errors) && count($errors) > 0;
	}
	return isset($flatten_page[$condition]) && $flatten_page[$condition];
}

function expand_condition($matches)
{
	global $page, $ifregexp;
	$value = check_condition($matches[2]) ^ ($matches[1] != 'if');
	if ($value) {
		return preg_replace_callback($ifregexp, "expand_condition", $matches[3]);
	} else if (isset($matches[4])) {
		return preg_replace_callback($ifregexp, "expand_condition", substr($matches[4], strpos($matches[4], "}") + 1));
	}
	return "";
}

function expand_var($matches)
{
	global $page, $mibewroot, $errors, $current_style, $flatten_page;

	$prefix = $matches[1];
	$var = $matches[2];
	if (!$prefix) {
		if ($var == 'mibewroot') {
			return $mibewroot;
		} else if ($var == 'tplroot') {
			return "$mibewroot/styles/dialogs/$current_style";
		} else if ($var == 'styleid') {
			return $current_style;
		} else if ($var == 'pagination') {
			return generate_pagination($page['pagination']);
		} else if ($var == 'errors' || $var == 'harderrors') {
			if (isset($errors) && count($errors) > 0) {
				$result = getlocal("$var.header");
				foreach ($errors as $e) {
					$result .= getlocal("errors.prefix") . $e . getlocal("errors.suffix");
				}
				$result .= getlocal("errors.footer");
				return $result;
			}
		}

	} else if ($prefix == 'msg:' || $prefix == 'msgjs:' || $prefix == 'url:') {
		$message = '';
		if (strpos($var, ",") !== false) {
			$pos = strpos($var, ",");
			$param = substr($var, $pos + 1);
			$var = substr($var, 0, $pos);
			$message = getlocal2($var, array($flatten_page[$param]));
		} else {
			$message = getlocal($var);
		}
		if ($prefix == 'msgjs:') {
			return json_encode($message);
		}
		return $message;
	} else if ($prefix == 'form:') {
		return form_value($var);
	} else if ($prefix == 'page:' || $prefix == 'pagejs:') {
		$message = isset($flatten_page[$var]) ? $flatten_page[$var] : "";
		return ($prefix == 'pagejs:') ? json_encode($message) : $message;
	} else if ($prefix == 'if:' || $prefix == 'else:' || $prefix == 'endif:' || $prefix == 'ifnot:') {
		return "<!-- wrong $prefix:$var -->";
	}

	return "";
}

function expand_include($matches)
{
	global $expand_include_path;
	$name = $matches[1];
	$contents = @file_get_contents($expand_include_path . $name) or die("cannot load template");
	return $contents;
}

function expandtext($text)
{
	global $ifregexp;
	$text = preg_replace_callback("/\\\${include:([\w\.]+)}/", "expand_include", $text);
	$text = preg_replace_callback($ifregexp, "expand_condition", $text);
	return preg_replace_callback("/\\\${(\w+:)?([\w\.,]+)}/", "expand_var", $text);
}

function expand($basedir, $style, $filename)
{
	global $page, $expand_include_path, $current_style, $flatten_page;

	$flatten_page = array_flatten_recursive($page);

	start_html_output();
	if (!is_dir("$basedir/$style")) {
		$style = "default";
	}
	$expand_include_path = "$basedir/$style/templates/";
	$current_style = $style;
	$contents = @file_get_contents($expand_include_path . $filename);
	if ($contents === false) {
		$expand_include_path = "$basedir/default/templates/";
		$current_style = "default";
		$contents = @file_get_contents($expand_include_path . $filename) or die("cannot load template");
	}
	echo expandtext($contents);
}

?>