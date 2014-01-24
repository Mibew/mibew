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

$page['title'] = getlocal("page_agent.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php if( $page['opid'] ) { ?>
<?php echo getlocal("page_agent.intro") ?>
<?php } ?>
<?php if( !$page['opid'] ) { ?>
<?php echo getlocal("page_agent.create_new") ?>
<?php } ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>
<?php if( $page['needChangePassword'] ) { ?>
<div id="formmessage"><?php echo getlocal("error.no_password") ?></div>
<br/>
<?php } else if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>



<?php if( $page['opid'] || $page['canmodify'] ) { ?>
<form name="agentForm" method="post" action="<?php echo $mibewroot ?>/operator/operator.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="opid" value="<?php echo safe_htmlspecialchars($page['opid']) ?>"/>
<?php if(!$page['showjabber']) { ?>
<input type="hidden" name="jabber" value="<?php echo form_value('jabber') ?>"/>
<?php if(form_value_cb('jabbernotify')) { ?><input type="hidden" name="jabbernotify" value="on"/><?php } ?>
<?php } ?> 
	<div>
<?php if(!$page['needChangePassword']) { print_tabbar(); } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.login') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="text" name="login" size="40" value="<?php echo form_value('login') ?>" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.login.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.mail') ?></div>
			<div class="fvalue">
				<input type="text" name="email" size="40" value="<?php echo form_value('email') ?>" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.mail.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.password') ?><?php if( !$page['opid'] || $page['needChangePassword'] ) { ?><span class="required">*</span><?php } ?></div>
			<div class="fvalue">
				<input type="password" name="password" size="40" value="" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?> autocomplete="off"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.password.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.password_confirm') ?><?php if( !$page['opid'] || $page['needChangePassword'] ) { ?><span class="required">*</span><?php } ?></div>
			<div class="fvalue">
				<input type="password" name="passwordConfirm" size="40" value="" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?> autocomplete="off"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.password_confirm.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.agent_name') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="text" name="name" size="40" value="<?php echo form_value('name') ?>" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.agent_name.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.agent_commonname') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="text" name="commonname" size="40" value="<?php echo form_value('commonname') ?>" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.agent_commonname.description') ?></div>
			<br clear="all"/>
		</div>

<?php if($page['showjabber']) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.jabber') ?></div>
			<div class="fvalue">
				<input type="text" name="jabber" size="40" value="<?php echo form_value('jabber') ?>" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.jabber.description') ?></div>
			<br clear="all"/>
		</div>
		
		<div class="field" style="padding-top:0.3em;">
			<div class="flabel"><?php echo getlocal('form.field.jabbernotify') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="jabbernotify" value="on"<?php echo form_value_cb('jabbernotify') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.jabbernotify.description') ?></div>
			<br clear="all"/>
		</div>
<?php } ?>

<?php if($page['canmodify']) { ?>
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
<?php } ?>
<?php
} /* content */

require_once('inc_main.php');
?>