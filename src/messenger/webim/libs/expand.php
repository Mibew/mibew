<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

$ifregexp = "/\\\${(if|ifnot):([\w\.]+)}(.*?)(\\\${else:\\2}.*?)?\\\${endif:\\2}/s";

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
	global $page, $webimroot, $errors;
	$prefix = $matches[1];
	$var = $matches[2];
	if(!$prefix) {
		if($var == 'webimroot') {
			return $webimroot;
		} else if($var == 'pagination') {
			return generate_pagination($page['pagination']);
		} else if($var == 'errors') {
			if( isset($errors) && count($errors) > 0 ) {
				$result = getlocal("errors.header");
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
		return $page[$var];
	} else if($prefix == 'print:') {
		return htmlspecialchars($page[$var]);
	} else if($prefix == 'if:' || $prefix == 'else:' || $prefix == 'endif:' || $prefix == 'ifnot:') {
		return "<!-- wrong $prefix:$var -->";
	}

	return "";
}

function expandtext($text) {
	global $ifregexp;
	$text = preg_replace_callback($ifregexp, "expand_condition", $text);
	return preg_replace_callback("/\\\${(\w+:)?([\w\.,]+)}/", "expand_var", $text);
}

function expand($basedir,$style,$filename) {
	start_html_output();
	if(!is_dir("$basedir/$style")) {
		$style = "default";
	}
	$contents = @file_get_contents("$basedir/$style/$filename");
	if($contents === false) {
		$contents = @file_get_contents("$basedir/default/$filename") or die("cannot load template");
	}
	echo expandtext($contents);
}

?>