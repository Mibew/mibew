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
require_once('../libs/settings.php');

$operator = check_login();

$errors = array();
$page = array(
	'localizations' => get_available_locales(),
	'phpVersion' => phpversion(),
	'version' => $version,
);

prepare_menu($operator);
setup_settings_tabs(3);
start_html_output();
require('../view/updates.php');
?>