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

<form name="settings" method="post" action="<?php echo $mibewroot ?>/operator/settings.php">
<?php print_csrf_token_input() ?>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.email') ?></div>
			<div class="fvalue">
				<input type="text" name="email" size="40" value="<?php echo form_value('email') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.email.description') ?></div>
			<br clear="all"/>
		</div>

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
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.chat.title.description') ?></div>
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

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.geolink') ?></div>
			<div class="fvalue">
				<input type="text" name="geolink" size="40" value="<?php echo form_value('geolink') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.geolink.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.geolinkparams') ?></div>
			<div class="fvalue">
				<input type="text" name="geolinkparams" size="40" value="<?php echo form_value('geolinkparams') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.geolinkparams.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.usernamepattern') ?></div>
			<div class="fvalue">
				<input type="text" name="usernamepattern" size="40" value="<?php echo form_value('usernamepattern') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.usernamepattern.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.chatstyle') ?></div>
			<div class="fvalue">
				<select name="chatstyle" ><?php foreach($page['availableStyles'] as $k) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("chatstyle") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k) . "</option>"; } ?></select>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.chatstyle.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.sendmessagekey') ?></div>
			<div class="fvaluenodesc">
				<label>
					<input type="radio" name="sendmessagekey" value="enter" <?php echo form_value("sendmessagekey") == "enter" ? " checked=\"checked\"" : ""?>/>Enter</label>
				<label>	
					<input type="radio" name="sendmessagekey" value="center" <?php echo form_value("sendmessagekey") == "center" ? " checked=\"checked\"" : ""?>/>Ctrl-Enter</label>
			</div>
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