<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

$ifregexp = "/\\\${(if|ifnot):([\w\.]+)}(.*?)(\\\${else:\\2}.*?)?\\\${endif:\\2}/s";
$expand_include_path = "";
$current_style = "";

function check_condition($condition) {
	global $errors, $page;
	if($condition == 'errors') {
		return isset($errors) && count($errors) > 0;
	}
	return isset($page[$condition]) && $page[$condition];
}

function expand_condition($matches) {
	global $page, $ifregexp;
	$value = check_condition($matches[2]) ^ ($matches[1] != 'if');
	if($value) {
		return preg_replace_callback($ifregexp, "expand_condition", $matches[3]);
	} else if(isset($matches[4])) {
		return preg_replace_callback($ifregexp, "expand_condition", substr($matches[4],strpos($matches[4],"}")+1));
	}
	return "";
}

function expand_var($matches) {
	global $page, $webimroot, $jsver, $errors, $current_style;
	$prefix = $matches[1];
	$var = $matches[2];
	if(!$prefix) {
		if($var == 'webimroot') {
			return $webimroot;
		} else if($var == 'jsver') {
			return $jsver;
		} else if($var == 'tplroot') {
			return "$webimroot/styles/$current_style";
		} else if($var == 'styleid') {
			return $current_style;
		} else if($var == 'pagination') {
			return generate_pagination($page['pagination']);
		} else if($var == 'errors' || $var == 'harderrors') {
			if( isset($errors) && count($errors) > 0 ) {
				$result = getlocal("$var.header");
				foreach( $errors as $e ) {
					$result .= getlocal("errors.prefix").$e.getlocal("errors.suffix");
				}
				$result .= getlocal("errors.footer");
				return $result;
			}
		}

	} else if($prefix == 'msg:' || $prefix == 'url:') {
		if(strpos($var,",")!==false) {
			$pos = strpos($var,",");
			$param = substr($var, $pos+1);
			$var = substr($var, 0, $pos);
			return getlocal2($var, array($page[$param]));
		}
		return getlocal($var);
	} else if($prefix == 'form:') {
		return form_value($var);
	} else if($prefix == 'page:') {
		return isset($page[$var]) ? $page[$var] : "";
	} else if($prefix == 'if:' || $prefix == 'else:' || $prefix == 'endif:' || $prefix == 'ifnot:') {
		return "<!-- wrong $prefix:$var -->";
	}

	return "";
}

function expand_include($matches) {
	global $expand_include_path;
	$name = $matches[1];
	$contents = @file_get_contents($expand_include_path.$name) or die("cannot load template");
	return $contents;
}

function expandtext($text) {
	global $ifregexp;
	$text = preg_replace_callback("/\\\${include:([\w\.]+)}/", "expand_include", $text);
	$text = preg_replace_callback($ifregexp, "expand_condition", $text);
	return preg_replace_callback("/\\\${(\w+:)?([\w\.,]+)}/", "expand_var", $text);
}

function expand($basedir,$style,$filename) {
	global $expand_include_path, $current_style;
	start_html_output();
	if(!is_dir("$basedir/$style")) {
		$style = "default";
	}
	$expand_include_path = "$basedir/$style/templates/";
	$current_style = $style;
	$contents = @file_get_contents($expand_include_path.$filename);
	if($contents === false) {
		$expand_include_path = "$basedir/default/templates/";
		$current_style = "default";
		$contents = @file_get_contents($expand_include_path.$filename) or die("cannot load template");
	}
	echo expandtext($contents);
}

?>