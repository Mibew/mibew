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

require_once('../libs/common.php');
require_once('../libs/operator.php');

$operator = check_login();
loadsettings();

$page = array(
	'operator' => topage(get_operator_name($operator)),
	'version' => $version,
	'localeLinks' => get_locale_links("$webimroot/operator/index.php"),
	'showban' => $settings['enableban'] == "1",
	'showadmin' => is_capable($can_administrate, $operator),
);

start_html_output();
require('../view/menu.php');
?>