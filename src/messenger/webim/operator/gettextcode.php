<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2010 Mibew Messenger Community
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
require_once('../libs/operator.php');
require_once('../libs/groups.php');
require_once('../libs/getcode.php');

$operator = check_login();
loadsettings();

$stylelist = get_style_list("../styles");
$style = verifyparam("style","/^\w*$/", "");
if($style && !in_array($style, $stylelist)) {
	$style = "";
}

$groupid = verifyparam_groupid("group");
$showhost = verifyparam("hostname","/^on$/", "") == "on";
$forcesecure = verifyparam("secure","/^on$/", "") == "on";
$modsecurity = verifyparam("modsecurity","/^on$/", "") == "on";

$allLocales = get_available_locales();

$lang = verifyparam("lang", "/^[\w-]{2,5}$/", "");
if( !$lang || !in_array($lang,$allLocales) )
	$lang = in_array($current_locale,$allLocales) ? $current_locale : $allLocales[0];

$message = "Click to chat"; // TODO

$page = array();
$page['buttonCode'] = generate_button("",$lang,$style,$groupid,$message,$showhost,$forcesecure,$modsecurity);
$page['availableLocales'] = $allLocales;
$page['availableStyles'] = $stylelist;
$page['groups'] = get_groups_list();

$page['formgroup'] = $groupid;
$page['formstyle'] = $style;
$page['formlang'] = $lang;
$page['formhostname'] = $showhost;
$page['formsecure'] = $forcesecure;
$page['formmodsecurity'] = $modsecurity;

prepare_menu($operator);
setup_getcode_tabs(1);
start_html_output();
require('../view/getcode_text.php');
?>