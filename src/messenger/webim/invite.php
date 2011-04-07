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
 *    Fedor Fetisov - tracking and inviting implementation
 */

require_once('libs/common.php');
require_once('libs/invitation.php');
require_once('libs/operator.php');
require_once('libs/track.php');

loadsettings();

$invited = FALSE;
$operator = array();
if ($settings['enabletracking'] == '1') {

    $entry = isset($_GET['entry']) ? $_GET['entry'] : "";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";

    $link = connect();

    if (isset($_SESSION['visitorid']) && preg_match('/^[0-9]+$/', $_SESSION['visitorid'])) {
	$invited = invitation_check($_SESSION['visitorid'], $link);
	$visitorid = track_visitor($_SESSION['visitorid'], $entry, $referer, $link);
    }
    else {
	$visitorid = track_visitor_start($entry, $referer, $link);
    }

    if ($visitorid) {
	$_SESSION['visitorid'] = $visitorid;
    }

    if ($invited !== FALSE) {
	$operator = operator_by_id_($invited, $link);
    }

    mysql_close($link);
}

start_xml_output();
if ($invited !== FALSE) {
    $locale = isset($_GET['lang']) ? $_GET['lang'] : '';
    $operatorName = ($locale == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];
    echo "<invitation><operator>" . htmlspecialchars($operatorName) . "</operator><message>" . getlocal("invitation.message") . "</message><avatar>" . htmlspecialchars($operator['vcavatar']) . "</avatar></invitation>";
}
else {
    echo "<empty/>";
}

exit;
?>