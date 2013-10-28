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

function tpl_header() { global $page, $mibewroot;
?>
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/libs/jquery.min.js"></script>
<script type="text/javascript" language="javascript">

function updateParentGroup() {
	if($("#parentgroup").val() == '') {
		$("#extrafields").show();
	}else{
		$("#extrafields").hide();
	}
}

$(function(){
	$("#parentgroup").change(function() {
		updateParentGroup();
	});
	updateParentGroup();
});
</script>
<?php
}

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
require_once(dirname(__FILE__).'/inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="groupForm" method="post" action="<?php echo $mibewroot ?>/operator/group.php">
<input type="hidden" name="gid" value="<?php echo $page['grid'] ?>"/>
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

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.groupweight') ?></div>
			<div class="fvalue">
				<input type="text" name="weight" size="40" value="<?php echo form_value('weight') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.groupweight.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.groupparent') ?></div>
			<div class="fvalue">
				<select name="parentgroup" id="parentgroup"><?php foreach($page['availableParentGroups'] as $k) { echo "<option value=\"".$k['groupid']."\"".($k['groupid'] == form_value("parentgroup") ? " selected=\"selected\"" : "").">".str_repeat('&nbsp;', $k['level']*2).$k['vclocalname']."</option>"; } ?></select>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.groupparent.description') ?></div>
			<br clear="all"/>
		</div>

		<div id="extrafields">
			<div class="fheader"><?php echo getlocal('page.group.extrafields.title') ?></div>

			<div class="field">
				<div class="flabel"><?php echo getlocal('settings.company.title') ?></div>
				<div class="fvalue">
					<input type="text" name="title" size="40" value="<?php echo form_value('title') ?>" class="formauth"/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.company.title.description') ?></div>
				<br clear="all"/>
			</div>

			<div class="field">
				<div class="flabel"><?php echo getlocal('settings.chat.title') ?></div>
				<div class="fvalue">
					<input type="text" name="chattitle" size="40" value="<?php echo form_value('chattitle') ?>" class="formauth"/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.chat.title') ?></div>
				<br clear="all"/>
			</div>

			<div class="field">
				<div class="flabel"><?php echo getlocal('settings.logo') ?></div>
				<div class="fvalue">
					<input type="text" name="logo" size="40" value="<?php echo form_value('logo') ?>" class="formauth"/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.logo.description') ?></div>
				<br clear="all"/>
			</div>

			<div class="field">
				<div class="flabel"><?php echo getlocal('settings.host') ?></div>
				<div class="fvalue">
					<input type="text" name="hosturl" size="40" value="<?php echo form_value('hosturl') ?>" class="formauth"/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.host.description') ?></div>
				<br clear="all"/>
			</div>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $mibewroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
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