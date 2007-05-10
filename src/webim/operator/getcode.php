<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require('../libs/common.php');
require('../libs/operator.php');

$operator = check_login();

$lang = verifyparam("lang", "/^\w\w$/", "");
if( !$lang || !in_array($lang,$available_locales) )
	$lang = $current_locale;

$file = "../images/webim/webim_${lang}_on.gif";
$size = get_gifimage_size($file);

$message = get_image("/webim/button.php?lang=$lang",$size[0],$size[1]);

$page = array();
$page['operator'] = get_operator_name($operator);
$page['buttonCode'] = generate_button("",$lang,$message);

start_html_output();
require('../view/gen_button.php');
?>