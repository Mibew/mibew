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

$page = array('agentId' => '', 'avatar' => '');
$page['operator'] = topage(get_operator_name($operator));
$errors = array();

if( isset($_POST['agentId']) ) {
	$avatar = '';
	$agentId = verifyparam( "agentId", "/^(\d{1,9})?$/", "");
	$op = operator_by_id($agentId);
	$login = $op ? $op['vclogin'] : '';

	if( !$op ) {
		$errors[] = getlocal("no_such_operator");

	} else if( isset($_FILES['avatarFile']) && $_FILES['avatarFile']['name']) {
        $valid_types = array("gif","jpg", "png", "tif");

        $orig_filename = $_FILES['avatarFile']['name'];
        $tmp_file_name = $_FILES['avatarFile']['tmp_name'];

        $ext = substr($orig_filename, 1 + strrpos($orig_filename, "."));
        $new_file_name = "$agentId.$ext";

        if ($_FILES['avatarFile']['size'] > $max_uploaded_file_size) {
            $errors[] = failed_uploading_file($orig_filename, "errors.file.size.exceeded");
        } elseif(!in_array($ext, $valid_types)) {
            $errors[] = failed_uploading_file($orig_filename, "errors.invalid.file.type");
        } else {
            $avatar_local_dir = "../images/avatar/";
            $full_file_path = $avatar_local_dir.$new_file_name;
            if (file_exists($full_file_path)) {
                unlink($full_file_path);
            }
            if (!move_uploaded_file($_FILES['avatarFile']['tmp_name'], $full_file_path)) {
                $errors[] = failed_uploading_file($orig_filename, "errors.file.move.error");
            } else {
                $avatar = "$webimroot/images/avatar/$new_file_name";
            }
        }
    } else {
    	$errors[] = "No file selected";
    }

	if(count($errors) == 0) {
		update_operator_avatar($op['operatorid'],$avatar);

		if ($agentId && $avatar && $_SESSION['operator'] && $operator['operatorid'] == $agentId) {
			$_SESSION['operator']['vcavatar'] = $avatar;
		}
		header("Location: $webimroot/operator/avatar.php?op=".topage($op['vclogin']));
		exit;
	} else {
		$page['avatar'] =  topage($op ? $op['vcavatar'] : '');
		$page['agentId'] = $agentId;
		$page['formlogin'] = topage($login);
	}

} else {
	$login = verifyparam( 'op', "/^[\w_]+$/");
	$op = operator_by_login( $login );

	if( !$op ) {
		$errors[] = getlocal("no_such_operator");
		$page['formlogin'] = topage($login);
	} else {
		if (isset($_GET['delete']) && $_GET['delete'] == "true") {
			update_operator_avatar($op['operatorid'],'');
			header("Location: $webimroot/operator/avatar.php?op=".topage($op['vclogin']));
			exit;
		}
		$page['formlogin'] = topage($op['vclogin']);
		$page['agentId'] = topage($op['operatorid']);
		$page['avatar'] = topage($op['vcavatar']);
	}
}

$page['tabs'] = isset($login) ? array(
	getlocal("page_agent.tab.main") => "$webimroot/operator/operator.php?op=".topage($login),
	getlocal("page_agent.tab.avatar") => ""
) : array();

start_html_output();
require('../view/avatar.php');
?>