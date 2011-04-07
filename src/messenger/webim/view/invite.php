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

$page['title'] = getlocal("invitation.title");

function tpl_header() { global $page, $webimroot, $jsver;
?>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/<?php echo $jsver ?>/common.js"></script>
<script type="text/javascript" language="javascript"><!--
var updaterOptions = {
	url:"<?php echo $webimroot ?>/operator/invitationstate.php",wroot:"<?php echo $webimroot ?>",
	agentservl:"<?php echo $webimroot ?>/operator/agent.php", frequency:<?php echo $page['frequency'] ?>,
	visitor: "<?php echo $page['visitor'] ?>" };
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/<?php echo $jsver ?>/invite_op.js"></script>
<?php
}

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("invitation.sent"); ?>

<div class="ajaxWait"></div>

<?php
} /* content */

require_once('inc_main.php');
?>