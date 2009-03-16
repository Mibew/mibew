<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once("inc_menu.php");
$page['title'] = getlocal("page.department.title");
$page['menuid'] = "departments";

function tpl_content() { global $page, $webimroot, $errors;
?>

	<?php if( $page['depid'] ) { ?>
<?php echo getlocal("page.department.intro") ?>
<?php } ?>
<?php if( !$page['depid'] ) { ?>
<?php echo getlocal("page.department.create_new") ?>
<?php } ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>

<form name="departmentForm" method="post" action="<?php echo $webimroot ?>/operator/department.php">
<input type="hidden" name="dep" value="<?php echo $page['depid'] ?>"/>
	<div>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.depname') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="text" name="name" size="40" value="<?php echo form_value('name') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.depname.description') ?></div>
			<br clear="left"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.depdesc') ?></div>
			<div class="fvalue">
				<input type="text" name="description" size="40" value="<?php echo form_value('description') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.depdesc.description') ?></div>
			<br clear="left"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.depcommonname') ?></div>
			<div class="fvalue">
				<input type="text" name="commonname" size="40" value="<?php echo form_value('commonname') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.depcommonname.description') ?></div>
			<br clear="left"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.depcommondesc') ?></div>
			<div class="fvalue">
				<input type="text" name="commondescription" size="40" value="<?php echo form_value('commondescription') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.depcommondesc.description') ?></div>
			<br clear="left"/>
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
