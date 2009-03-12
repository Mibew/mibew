<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('inc_locales.php');
$page['title'] = getlocal("page_login.title");

function rightmenu_content() { global $page, $webimroot, $errors, $current_locale;
	$message = get_image(get_app_location(false,false)."/button.php?image=webim&amp;lang=$current_locale", 0, 0);
	$code = generate_button("",$current_locale,"",$message,false,false);
?>
			<li>
				<h2><b>contact us</b></h2>
				<?php echo $code ?>
			</li>
<?php 
}

function tpl_content() { global $page, $webimroot, $errors;
?>
<?php echo getlocal("page_login.intro") ?>
<br/>
<br/>

<?php 
require_once('inc_errors.php');
?>

<form name="loginForm" method="post" action="<?php echo $webimroot ?>/operator/login.php">
<input type="hidden" name="backPath" value="<?php echo $page['backPath'] ?>"/>
	<div id="loginpane">
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="fleftlabel"><?php echo getlocal("page_login.login") ?></div>
			<div class="fvalue">
				<input type="text" name="login" size="25" value="<?php echo form_value('login') ?>" class="formauth"/>
			</div>
			<br clear="left"/>
		</div>

		<div class="field">
			<div class="fleftlabel"><?php echo getlocal("page_login.password") ?></div>
			<div class="fvalue">
				<input type="password" name="password" size="25" value="" class="formauth"/>
			</div>
			<br clear="left"/>
		</div>

		<div class="field">
			<div class="fleftlabel">&nbsp;</div>
			<div class="fvalue">
				<label>
					<input type="checkbox" name="isRemember" value="on"<?php echo form_value_cb('isRemember') ? " checked=\"checked\"" : "" ?> />
					<?php echo getlocal("page_login.remember") ?></label>
			</div>
			<br clear="left"/>
		</div>

		<div class="fbutton">
			<input type="image" name="login" src='<?php echo $webimroot.getlocal("image.button.login") ?>' alt='<?php echo getlocal("button.enter") ?>'/>
		</div>

	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>

	<div class="asterisk">
		<?php echo getlocal("common.asterisk_explanation") ?>
	</div>

	</div>		
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>