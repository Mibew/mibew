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

require_once("inc_menu.php");
$page['title'] = getlocal("permissions.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("permissions.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="permissionsForm" method="post" action="<?php echo $webimroot ?>/operator/permissions.php">
<input type="hidden" name="op" value="<?php echo $page['opid'] ?>"/>
	<div>
<?php if($page['tabs']) { ?>
	<ul class="tabs">

<?php foreach($page['tabs'] as $k => $v) { if($v) { ?>
	<li><a href="<?php echo $v ?>"><?php echo $k ?></a></li>
<?php } else { ?>
	<li class="active"><a href="#"><?php echo $k ?></a></li><?php }} ?>
	</ul>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo $page['currentop'] ?>&lrm;</b>
	</p>
<?php foreach( $page['permissionsList'] as $pm ) { ?>
	<label>
		<input type="checkbox" name="permissions<?php echo $pm['id'] ?>" value="on"<?php echo form_value_mb('permissions',$pm['id']) ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/> <?php echo $pm['descr'] ?>
	</label>
	<br/>
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