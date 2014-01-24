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

$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("page_settings.intro") ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("settings.saved") ?></div>
<?php } ?>

<form name="performance" method="post" action="<?php echo $mibewroot ?>/operator/performance.php">
<?php print_csrf_token_input() ?>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.onlinetimeout') ?></div>
			<div class="fvalue">
				<input type="text" name="onlinetimeout" size="40" value="<?php echo form_value('onlinetimeout') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.onlinetimeout.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.frequencyoperator') ?></div>
			<div class="fvalue">
				<input type="text" name="frequencyoperator" size="40" value="<?php echo form_value('frequencyoperator') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.frequencyoperator.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.frequencychat') ?></div>
			<div class="fvalue">
				<input type="text" name="frequencychat" size="40" value="<?php echo form_value('frequencychat') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.frequencychat.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.frequencyoldchat') ?></div>
			<div class="fvalue">
				<input type="text" name="frequencyoldchat" size="40" value="<?php echo form_value('frequencyoldchat') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.frequencyoldchat.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.onehostconnections') ?></div>
			<div class="fvalue">
				<input type="text" name="onehostconnections" size="40" value="<?php echo form_value('onehostconnections') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.onehostconnections.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.threadlifetime') ?></div>
			<div class="fvalue">
				<input type="text" name="threadlifetime" size="40" value="<?php echo form_value('threadlifetime') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.threadlifetime.description') ?></div>
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