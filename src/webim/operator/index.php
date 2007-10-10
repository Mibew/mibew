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

$page = array( 
	'operator' => get_operator_name($operator),
	'version' => 'v1.0.7'
);

$localeLinks = "";
foreach($available_locales as $k) {
	if( strlen($localeLinks) > 0 )
		$localeLinks .= " &bull; ";
	if( $k == $current_locale )
		$localeLinks .= $k;
	else
		$localeLinks .= "<a href=\"/webim/operator/index.php?locale=$k\">$k</a>";
}

$page['localeLinks'] = $localeLinks;

start_html_output();
require('../view/menu.php');
?>