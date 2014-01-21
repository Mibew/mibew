<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

require_once(dirname(__FILE__).'/inc_menu.php');
require_once(dirname(__FILE__).'/inc_tabbar.php');

function tpl_header() { global $page;
?>
<script type="text/javascript" language="javascript" src="<?php echo MIBEW_WEB_ROOT ?>/js/libs/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/js/group.js"></script>
<?php
}

function tpl_content() { global $page;
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
require_once(dirname(__FILE__).'/inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="groupForm" method="post" action="<?php echo MIBEW_WEB_ROOT ?>/operator/group.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="gid" value="<?php echo $page['grid'] ?>"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<label for="name" class="flabel"><?php echo getlocal('form.field.groupname') ?><span class="required">*</span></label>
			<div class="fvalue">
				<input id="name" type="text" name="name" size="40" value="<?php echo form_value($page, 'name') ?>" class="formauth"/>
			</div>
			<label for="name" class="fdescr"> &mdash; <?php echo getlocal('form.field.groupname.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="description" class="flabel"><?php echo getlocal('form.field.groupdesc') ?></label>
			<div class="fvalue">
				<input id="description" type="text" name="description" size="40" value="<?php echo form_value($page, 'description') ?>" class="formauth"/>
			</div>
			<label for="description" class="fdescr"> &mdash; <?php echo getlocal('form.field.groupdesc.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="commonname" class="flabel"><?php echo getlocal('form.field.groupcommonname') ?></label>
			<div class="fvalue">
				<input id="commonname" type="text" name="commonname" size="40" value="<?php echo form_value($page, 'commonname') ?>" class="formauth"/>
			</div>
			<label for="commonname" class="fdescr"> &mdash; <?php echo getlocal('form.field.groupcommonname.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="commondescription" class="flabel"><?php echo getlocal('form.field.groupcommondesc') ?></label>
			<div class="fvalue">
				<input id="commondescription" type="text" name="commondescription" size="40" value="<?php echo form_value($page, 'commondescription') ?>" class="formauth"/>
			</div>
			<label for="commondescription" class="fdescr"> &mdash; <?php echo getlocal('form.field.groupcommondesc.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="email" class="flabel"><?php echo getlocal('form.field.mail') ?></label>
			<div class="fvalue">
				<input id="email" type="text" name="email" size="40" value="<?php echo form_value($page, 'email') ?>" class="formauth"/>
			</div>
			<label for="email" class="fdescr"> &mdash; <?php echo getlocal('form.field.groupemail.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="weight" class="flabel"><?php echo getlocal('form.field.groupweight') ?></label>
			<div class="fvalue">
				<input id="weight" type="text" name="weight" size="40" value="<?php echo form_value($page, 'weight') ?>" class="formauth"/>
			</div>
			<label for="weight" class="fdescr"> &mdash; <?php echo getlocal('form.field.groupweight.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="parentgroup" class="flabel"><?php echo getlocal('form.field.groupparent') ?></label>
			<div class="fvalue">
				<select name="parentgroup" id="parentgroup"><?php foreach($page['availableParentGroups'] as $k) { echo "<option value=\"".$k['groupid']."\"".($k['groupid'] == form_value($page, "parentgroup") ? " selected=\"selected\"" : "").">".str_repeat('&nbsp;', $k['level']*2).$k['vclocalname']."</option>"; } ?></select>
			</div>
			<label for="parentgroup" class="fdescr"> &mdash; <?php echo getlocal('form.field.groupparent.description') ?></label>
			<br clear="all"/>
		</div>

		<div id="extrafields">
			<div class="fheader"><?php echo getlocal('page.group.extrafields.title') ?></div>

			<div class="field">
				<label for="titlefield" class="flabel"><?php echo getlocal('settings.company.title') ?></label>
				<div class="fvalue">
					<input id="titlefield" type="text" name="title" size="40" value="<?php echo form_value($page, 'title') ?>" class="formauth"/>
				</div>
				<label for="titlefield" class="fdescr"> &mdash; <?php echo getlocal('settings.company.title.description') ?></label>
				<br clear="all"/>
			</div>

			<div class="field">
				<label for="chattitle" class="flabel"><?php echo getlocal('settings.chat.title') ?></label>
				<div class="fvalue">
					<input id="chattitle" type="text" name="chattitle" size="40" value="<?php echo form_value($page, 'chattitle') ?>" class="formauth"/>
				</div>
				<label for="chattitle" class="fdescr"> &mdash; <?php echo getlocal('settings.chat.title') ?></label>
				<br clear="all"/>
			</div>

			<div class="field">
				<label for="logofield" class="flabel"><?php echo getlocal('settings.logo') ?></label>
				<div class="fvalue">
					<input id="logofield" type="text" name="logo" size="40" value="<?php echo form_value($page, 'logo') ?>" class="formauth"/>
				</div>
				<label for="logofield" class="fdescr"> &mdash; <?php echo getlocal('settings.logo.description') ?></label>
				<br clear="all"/>
			</div>

			<div class="field">
				<label for="hosturl" class="flabel"><?php echo getlocal('settings.host') ?></label>
				<div class="fvalue">
					<input id="hosturl" type="text" name="hosturl" size="40" value="<?php echo form_value($page, 'hosturl') ?>" class="formauth"/>
				</div>
				<label for="hosturl" class="fdescr"> &mdash; <?php echo getlocal('settings.host.description') ?></label>
				<br clear="all"/>
			</div>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo MIBEW_WEB_ROOT . getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
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

require_once(dirname(__FILE__).'/inc_main.php');
?>