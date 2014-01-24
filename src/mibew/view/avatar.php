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

$page['title'] = getlocal("page_avatar.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("page_avatar.intro") ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>

<form name="avatarForm" method="post" action="<?php echo $mibewroot ?>/operator/avatar.php" enctype="multipart/form-data">
<?php print_csrf_token_input() ?>
<input type="hidden" name="op" value="<?php echo safe_htmlspecialchars($page['opid']) ?>"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo safe_htmlspecialchars($page['currentop']) ?>&lrm;</b>
	</p>

	<div class="fieldForm">

<?php if( $page['avatar'] ) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.avatar.current') ?></div>
			<div class="fvalue">
				<img src="<?php echo safe_htmlspecialchars($page['avatar']) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("page_avatar.cannot_load_avatar")) ?>"/><br/>
<?php if($page['canmodify']) { ?>
                <a class="formauth" href="<?php echo $mibewroot ?>/operator/avatar.php?op=<?php echo urlencode($page['opid']) ?>&amp;delete=true">
                    <?php echo getlocal("page_agent.clear_avatar") ?>
                </a>
<?php } ?>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.avatar.current.description') ?></div>
			<br clear="all"/>
		</div>
<?php } else if(!$page['canmodify']) { ?>
		<div class="field">
			<div class="fvaluenodesc">
				<?php echo getlocal('page_avatar.no_avatar') ?>
			</div>
		</div>
<?php } ?>

<?php if($page['canmodify']) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.avatar.upload') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="file" name="avatarFile" size="40" value="<?php echo form_value('avatarFile') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.avatar.upload.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src="<?php echo $mibewroot . safe_htmlspecialchars(getlocal("image.button.save")) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("button.save")) ?>"/>
		</div>
<?php } ?>
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