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

require_once('../libs/common.php');
require_once('../libs/operator.php');

$operator = check_login();
$status = isset($_GET['away']) ? 1 : 0;

notify_operator_alive($operator['operatorid'], $status);

loadsettings();
if($settings['enablegroups'] == '1') {
	$link = connect();
	$groupids = array(0);
	$allgroups = select_multi_assoc("select groupid from chatgroupoperator where operatorid = ".$operator['operatorid']." order by groupid",$link);
	foreach($allgroups as $g) {
		$groupids[] = $g['groupid'];	
	}
	$_SESSION['operatorgroups'] = implode(",", $groupids); 
	mysql_close($link);
} else {
	$_SESSION['operatorgroups'] = ""; 
}

$page = array();
$page['havemenu'] = isset($_GET['nomenu']) ? "0" : "1";
$page['showpopup'] = $settings['enablepopupnotification'] == '1' ? "1" : "0";
$page['frequency'] = $settings['updatefrequency_operator'];
$page['istatus'] = $status;

prepare_menu($operator);
start_html_output();
require('../view/pending_users.php');
?>