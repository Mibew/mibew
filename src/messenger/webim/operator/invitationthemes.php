<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
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

require_once('../libs/common.php');
require_once('../libs/pagination.php');
require_once('../libs/operator.php');
require_once('../libs/groups.php');
require_once('../libs/expand.php');
require_once('../libs/settings.php');
require_once('../libs/styles.php');

$operator = check_login();

$stylelist = get_style_list("../styles/invitations");

$preview = verifyparam("preview", "/^\w+$/", "default");
if (!in_array($preview, $stylelist)) {
	$preview = $stylelist[0];
}

$page['formpreview'] = $preview;
$page['preview'] = $preview;
$page['availablePreviews'] = $stylelist;
$page['operatorName'] = (empty($operator['vclocalname'])?$operator['vccommonname']:$operator['vclocalname']);

prepare_menu($operator);
start_html_output();
setup_settings_tabs(4);
require('../view/invitation_themes.php');
?>