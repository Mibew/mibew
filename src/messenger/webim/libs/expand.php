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

function check_condition($condition) {
	global $errors, $page;
	if($condition == 'errors') {
		return isset($errors) && count($errors) > 0;
	}
	return isset($page[$condition]) && $page[$condition];
}

function expand_condition($matches) {
	global $page;
	$value = check_condition($matches[2]) ^ ($matches[1] != 'if');
	if($value) {
		return $matches[3];
	} else if($matches[4]) {
		return substr($matches[4],strpos($matches[4],"}")+1); 
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

	} else if($prefix == 'msg:') {
		return getlocal($var);
	} else if($prefix == 'form:') {
		return $page["form$var"];
	} else if($prefix == 'page:') {
		return $page["$var"];
	}

	return "";
}

function expandtext($text) {
	$text = preg_replace_callback("/\\\${(if|ifnot):([\w\.]+)}(.*?)(\\\${else:\\2}.*?)?\\\${endif:\\2}/sm", "expand_condition", $text);
	return preg_replace_callback("/\\\${(\w+:)?([\w\.]+)}/", "expand_var", $text);
}

function expand($filename) {
	start_html_output();
	$contents = file_get_contents($filename) or die("illegal template");
	echo expandtext($contents);
}

?>