<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

notify_operator_alive($operator['operatorid']);

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

prepare_menu($operator);
start_html_output();
require('../view/pending_users.php');
?>