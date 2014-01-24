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

$ifregexp = "/\\\${(if|ifnot):([\w\.]+)}(.*?)(\\\${else:\\2}.*?)?\\\${endif:\\2}/s";
$expand_include_path = dirname(__FILE__) . '/../';
$current_style = "";

function check_condition($condition)
{
	global $errors, $page;
	if ($condition == 'errors') {
		return isset($errors) && count($errors) > 0;
	}
	return isset($page[$condition]) && $page[$condition];
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
	global $page, $mibewroot, $jsver, $errors, $current_style;
	$prefix = $matches[1];
	$var = $matches[2];
	if (!$prefix) {
		if ($var == 'mibewroot') {
			return $mibewroot;
		} else if ($var == 'jsver') {
			return $jsver;
		} else if ($var == 'tplroot') {
			return "$mibewroot/styles/$current_style";
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

	} else if ($prefix == 'msg:' || $prefix == 'url:') {
		if (strpos($var, ",") !== false) {
			$pos = strpos($var, ",");
			$param = substr($var, $pos + 1);
			$var = substr($var, 0, $pos);
			return getlocal2($var, array($page[$param]));
		}
		return getlocal($var);
	} else if ($prefix == 'form:') {
		return form_value($var);
	} else if ($prefix == 'page:') {
		return isset($page[$var]) ? $page[$var] : "";
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
	global $expand_include_path, $current_style;
	start_html_output();
	if (!preg_match('/^\w+$/', $style) || !is_dir("$basedir/$style")) {
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