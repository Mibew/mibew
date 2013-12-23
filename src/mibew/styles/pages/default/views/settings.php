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

$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("page_settings.intro") ?>
<br />
<br />
<?php 
require_once(dirname(__FILE__).'/inc_errors.php');
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
			<label for="email" class="flabel"><?php echo getlocal('settings.email') ?></label>
			<div class="fvalue">
				<input id="email" type="text" name="email" size="40" value="<?php echo form_value('email') ?>" class="formauth"/>
			</div>
			<label for="email" class="fdescr"> &mdash; <?php echo getlocal('settings.email.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="titlefield" class="flabel"><?php echo getlocal('settings.company.title') ?></label>
			<div class="fvalue">
				<input id="titlefield" type="text" name="title" size="40" value="<?php echo form_value('title') ?>" class="formauth"/>
			</div>
			<label for="titlefield" class="fdescr"> &mdash; <?php echo getlocal('settings.company.title.description') ?></label>
			<br clear="all"/>
		</div>
		 
		<div class="field">
			<label for="chattitle" class="flabel"><?php echo getlocal('settings.chat.title') ?></label>
			<div class="fvalue">
				<input id="chattitle" type="text" name="chattitle" size="40" value="<?php echo form_value('chattitle') ?>" class="formauth"/>
			</div>
			<label for="chattitle" class="fdescr"> &mdash; <?php echo getlocal('settings.chat.title.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="logofield" class="flabel"><?php echo getlocal('settings.logo') ?></label>
			<div class="fvalue">
				<input id="logofield" type="text" name="logo" size="40" value="<?php echo form_value('logo') ?>" class="formauth"/>
			</div>
			<label for="logofield" class="fdescr"> &mdash; <?php echo getlocal('settings.logo.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="hosturl" class="flabel"><?php echo getlocal('settings.host') ?></label>
			<div class="fvalue">
				<input id="hosturl" type="text" name="hosturl" size="40" value="<?php echo form_value('hosturl') ?>" class="formauth"/>
			</div>
			<label for="hosturl" class="fdescr"> &mdash; <?php echo getlocal('settings.host.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="geolink" class="flabel"><?php echo getlocal('settings.geolink') ?></label>
			<div class="fvalue">
				<input id="geolink" type="text" name="geolink" size="40" value="<?php echo form_value('geolink') ?>" class="formauth"/>
			</div>
			<label for="geolink" class="fdescr"> &mdash; <?php echo getlocal('settings.geolink.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="geolinkparams" class="flabel"><?php echo getlocal('settings.geolinkparams') ?></label>
			<div class="fvalue">
				<input id="geolinkparams" type="text" name="geolinkparams" size="40" value="<?php echo form_value('geolinkparams') ?>" class="formauth"/>
			</div>
			<label for="geolinkparams" class="fdescr"> &mdash; <?php echo getlocal('settings.geolinkparams.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="usernamepattern" class="flabel"><?php echo getlocal('settings.usernamepattern') ?></label>
			<div class="fvalue">
				<input id="usernamepattern" type="text" name="usernamepattern" size="40" value="<?php echo form_value('usernamepattern') ?>" class="formauth"/>
			</div>
			<label for="usernamepattern" class="fdescr"> &mdash; <?php echo getlocal('settings.usernamepattern.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="cronkey" class="flabel"><?php echo getlocal('settings.cronkey') ?></label>
			<div class="fvalue">
				<input id="cronkey" type="text" name="cronkey" size="40" value="<?php echo form_value('cronkey') ?>" class="formauth"/>
			</div>
			<label for="cronkey" class="fdescr"> &mdash; <?php echo getlocal2('settings.cronkey.description', array($page['cron_path'])) ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="page_style" class="flabel"><?php echo getlocal('settings.page_style') ?></label>
			<div class="fvalue">
				<select id="pages_style" name="page_style" ><?php foreach($page['availablePageStyles'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("pagestyle") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
			<label for="page_style" class="fdescr"> &mdash; <?php echo getlocal('settings.page_style.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="chat_style" class="flabel"><?php echo getlocal('settings.chatstyle') ?></label>
			<div class="fvalue">
				<select id="chat_style" name="chat_style" ><?php foreach($page['availableChatStyles'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("chatstyle") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
			<label for="chat_style" class="fdescr"> &mdash; <?php echo getlocal('settings.chatstyle.description') ?></label>
			<br clear="all"/>
		</div>
<?php if ($page['enabletracking']) { ?>
		<div class="field">
			<label for="invitation_style" class="flabel"><?php echo getlocal('settings.invitationstyle') ?></label>
			<div class="fvalue">
				<select id="invitation_style" name="invitation_style" ><?php foreach($page['availableInvitationStyles'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("invitationstyle") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
			<label for="invitation_style" class="fdescr"> &mdash; <?php echo getlocal('settings.invitationstyle.description') ?></label>
			<br clear="all"/>
		</div>
<?php } ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.sendmessagekey') ?></div>
			<div class="fvaluenodesc">
				<input id="sendmessagekeyenter" type="radio" name="sendmessagekey" value="enter" <?php echo form_value("sendmessagekey") == "enter" ? " checked=\"checked\"" : ""?>/><label for="sendmessagekeyenter">Enter</label>
				<input id="sendmessagekeycenter" type="radio" name="sendmessagekey" value="center" <?php echo form_value("sendmessagekey") == "center" ? " checked=\"checked\"" : ""?>/><label for="sendmessagekeycenter">Ctrl-Enter</label>
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