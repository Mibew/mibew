<?php
/*
 * Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once("inc_menu.php");
require_once("inc_tabbar.php");

$page['title'] = getlocal("page.group.title");
$page['menuid'] = "groups";

function tpl_content() { global $page, $mibewroot, $errors;
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

<form name="groupForm" method="post" action="<?php echo $mibewroot ?>/operator/group.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="gid" value="<?php echo safe_htmlspecialchars($page['grid']) ?>"/>
	<div>
<?php print_tabbar(); ?>
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
		
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.mail') ?></div>
			<div class="fvalue">
				<input type="text" name="email" size="40" value="<?php echo form_value('email') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.groupemail.description') ?></div>
			<br clear="all"/>
		</div>
		
		<div class="fbutton">
			<input type="image" name="save" value="" src="<?php echo $mibewroot . safe_htmlspecialchars(getlocal("image.button.save")) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("button.save")) ?>"/>
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