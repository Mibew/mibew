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

function tpl_content() { global $page;
?>

<?php echo getlocal("page_avatar.intro") ?>
<br />
<br />
<?php 
require_once(dirname(__FILE__).'/inc_errors.php');
?>

<form name="avatarForm" method="post" action="<?php echo MIBEW_WEB_ROOT ?>/operator/avatar.php" enctype="multipart/form-data">
<?php print_csrf_token_input() ?>
<input type="hidden" name="op" value="<?php echo $page['opid'] ?>"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo $page['currentop'] ?>&lrm;</b>
	</p>

	<div class="fieldForm">

<?php if( $page['avatar'] ) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.avatar.current') ?></div>
			<div class="fvalue">
				<img src="<?php echo $page['avatar'] ?>" alt="cannot load avatar"/><br/>
<?php if($page['canmodify']) { ?>
                <a class="formauth" href='<?php echo MIBEW_WEB_ROOT ?>/operator/avatar.php?op=<?php echo $page['opid'] ?>&amp;delete=true'>
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
				No avatar
			</div>
		</div>
<?php } ?>

<?php if($page['canmodify']) { ?>
		<div class="field">
			<label for="avatarFile" class="flabel"><?php echo getlocal('form.field.avatar.upload') ?><span class="required">*</span></label>
			<div class="fvalue">
				<input id="avatarFile" type="file" name="avatarFile" size="40" value="<?php echo form_value($page, 'avatarFile') ?>" class="formauth"/>
			</div>
			<label for="avatarFile" class="fdescr"> &mdash; <?php echo getlocal('form.field.avatar.upload.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo MIBEW_WEB_ROOT . getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
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

require_once(dirname(__FILE__).'/inc_main.php');
?>