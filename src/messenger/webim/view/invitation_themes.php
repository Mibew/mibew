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

require_once("inc_menu.php");
require_once("inc_tabbar.php");

$page['title'] = getlocal("page.preview.title");
$page['menuid'] = "settings";

function tpl_header() { global $page, $webimroot;
?>
<link href="<?php echo $webimroot ?>/styles/invitations/<?php echo $page['preview'] ?>/invite.css" rel="stylesheet" type="text/css" />
<?
} /* header */

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page.preview.intro") ?>
<br />
<br />

<form name="preview" method="get" action="<?php echo $webimroot ?>/operator/invitationthemes.php">
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.preview.choose") ?></div>
			<div class="fvaluenodesc">
				<select name="preview" onchange="this.form.submit();"><?php foreach($page['availablePreviews'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("preview") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<div id="mibewinvitation">
	<div id="mibewinvitationpopup">
		<div id="mibewinvitationclose">
			<a onclick="void(0);" href="javascript:void(0);">&times;</a>
		</div>
		<h1 onclick="void(0);"><?php echo $page['operatorName'] ?></h1>
		<p onclick="void(0);"><?php echo getlocal("invitation.message"); ?></p>
		<div style="clear: both;"></div>
	</div>
</div>

<?php 
} /* content */

require_once('inc_main.php');
?>