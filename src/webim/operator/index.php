<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Internet Services Ltd.
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

$page = array( 
	'operator' => get_operator_name($operator),
	'version' => $version,
	'localeLinks' => get_locale_links("$webimroot/operator/index.php")
);

start_html_output();
require('../view/menu.php');
?>