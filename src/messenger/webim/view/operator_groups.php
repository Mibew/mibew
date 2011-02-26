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

$page['title'] = getlocal("operator.groups.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("operator.groups.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="opgroupsForm" method="post" action="<?php echo $webimroot ?>/operator/opgroups.php">
<input type="hidden" name="op" value="<?php echo $page['opid'] ?>"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo $page['currentop'] ?>&lrm;</b>
	</p>
<?php foreach( $page['groups'] as $pm ) { ?>
	<div class="field">
		<div class="flabel"><?php echo htmlspecialchars(topage($pm['vclocalname'])) ?></div>
		<div class="fvalue">
			<input type="checkbox" name="group<?php echo $pm['groupid'] ?>" value="on"<?php echo form_value_mb('group',$pm['groupid']) ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
		</div>
		<div class="fdescr"> &mdash; <?php echo $pm['vclocaldescription'] ? htmlspecialchars(topage($pm['vclocaldescription'])) : getlocal("operator.group.no_description") ?></div>
		<br clear="all"/>
	</div>
<?php } ?>

<?php if($page['canmodify']) { ?>
	<div class="fbutton">
		<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
	</div>
<?php } ?>
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>