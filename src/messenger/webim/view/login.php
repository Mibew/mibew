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

if(isset($page) && isset($page['localeLinks'])) {
	require_once('inc_locales.php');
}
$page['title'] = getlocal("page_login.title");
$page['headertitle'] = getlocal("app.title");
$page['show_small_login'] = true;
$page['fixedwrap'] = true;

function tpl_content() { global $page, $webimroot, $errors;
?>

<div id="loginintro">
<p><?php echo getlocal("app.descr")?></p>
</div>

<form name="loginForm" method="post" action="<?php echo $webimroot ?>/operator/login.php">
	<div id="loginpane">

	<div class="header">	
		<h2><?php echo getlocal("page_login.title") ?></h2>
	</div>

	<div class="fieldForm">
	
		<?php echo getlocal("page_login.intro") ?><br/><br/>

<?php 
require_once('inc_errors.php');
?>
	
		<div class="field">
			<div class="fleftlabel"><?php echo getlocal("page_login.login") ?></div>
			<div class="fvalue">
				<input type="text" name="login" size="25" value="<?php echo form_value('login') ?>" class="formauth"/>
			</div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="fleftlabel"><?php echo getlocal("page_login.password") ?></div>
			<div class="fvalue">
				<input type="password" name="password" size="25" value="" class="formauth"/>
			</div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="fleftlabel">&nbsp;</div>
			<div class="fvalue">
				<label>
					<input type="checkbox" name="isRemember" value="on"<?php echo form_value_cb('isRemember') ? " checked=\"checked\"" : "" ?> />
					<?php echo getlocal("page_login.remember") ?></label>
			</div>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<input type="image" name="login" src='<?php echo $webimroot.getlocal("image.button.login") ?>' alt='<?php echo getlocal("button.enter") ?>'/>

			<div class="links">
				<a href="restore.php"><?php echo getlocal("restore.pwd.message") ?></a><br/>
			</div>
		</div>

	</div>

	</div>		
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>