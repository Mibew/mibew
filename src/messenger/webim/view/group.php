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
$page['title'] = getlocal("page.group.title");
$page['menuid'] = "groups";

function tpl_content() { global $page, $webimroot, $errors;
?>

	<?php if( $page['grid'] ) { ?>
<?php echo getlocal("page.group.intro") ?>
<?php } ?>
<?php if( !$page['grid'] ) { ?>
<?php echo getlocal("page.group.create_new") ?>
<?php } ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="groupForm" method="post" action="<?php echo $webimroot ?>/operator/group.php">
<input type="hidden" name="gid" value="<?php echo $page['grid'] ?>"/>
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

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.groupname') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="text" name="name" size="40" value="<?php echo form_value('name') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.groupname.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.groupdesc') ?></div>
			<div class="fvalue">
				<input type="text" name="description" size="40" value="<?php echo form_value('description') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.groupdesc.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.groupcommonname') ?></div>
			<div class="fvalue">
				<input type="text" name="commonname" size="40" value="<?php echo form_value('commonname') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.groupcommonname.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.groupcommondesc') ?></div>
			<div class="fvalue">
				<input type="text" name="commondescription" size="40" value="<?php echo form_value('commondescription') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.groupcommondesc.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
	
	<div class="asterisk">
		<?php echo getlocal("common.asterisk_explanation") ?>
	</div>

</form>

<?php 
} /* content */

require_once('inc_main.php');
?>