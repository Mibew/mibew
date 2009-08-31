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
require_once('../libs/settings.php');

$errors = array();
$page = array('version' => $version);
$loginoremail = "";

if (isset($_POST['loginoremail'])) {
	$loginoremail = getparam("loginoremail");
	
	$torestore = is_valid_email($loginoremail) ? operator_by_email($loginoremail) : operator_by_login($loginoremail);
	if(!$torestore) {
		$errors[] = getlocal("no_such_operator");
	}
	
	$email = $torestore['vcemail'];
	if(count($errors) == 0 && !is_valid_email($email)) {
		$errors[] = "Operator hasn't set his e-mail";
	}
	
	if (count($errors) == 0) {
		$token = md5((time() + microtime()).rand(0,99999999));
		
		$link = connect();
		$query = "update chatoperator set dtmrestore = CURRENT_TIMESTAMP, vcrestoretoken = '$token' where operatorid = ".$torestore['operatorid'];
		perform_query($query, $link);
		mysql_close($link);
		
		$link = get_app_location(true,false)."/operator/resetpwd.php?id=".$torestore['operatorid']."&token=$token";
		
		webim_mail($email, $email, getstring("restore.mailsubj"), getstring2("restore.mailtext",array(get_operator_name($torestore), $link)));

		$page['isdone'] = true;
		require('../view/restore.php');
		exit;
    }
}

$page['formloginoremail'] = topage($loginoremail);

$page['localeLinks'] = get_locale_links("$webimroot/operator/restore.php");
$page['isdone'] = false;
start_html_output();
require('../view/restore.php');
?>