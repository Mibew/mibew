<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once("inc_menu.php");
require_once("inc_tabbar.php");

$page['title'] = getlocal("page_agent.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $webimroot, $errors;
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
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<?php if( $page['opid'] || $page['canmodify'] ) { ?>
<form name="agentForm" method="post" action="<?php echo $webimroot ?>/operator/operator.php">
<input type="hidden" name="opid" value="<?php echo $page['opid'] ?>"/>
	<div>
<?php print_tabbar(); ?>
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
			<div class="flabel"><?php echo getlocal('form.field.password') ?><?php if( !$page['opid'] ) { ?><span class="required">*</span><?php } ?></div>
			<div class="fvalue">
				<input type="password" name="password" size="40" value="" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.password.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.password_confirm') ?><?php if( !$page['opid'] ) { ?><span class="required">*</span><?php } ?></div>
			<div class="fvalue">
				<input type="password" name="passwordConfirm" size="40" value="" class="formauth"<?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
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

<?php if($page['canmodify']) { ?>
		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
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