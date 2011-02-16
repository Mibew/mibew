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
$page['title'] = getlocal("resetpwd.title");
$page['headertitle'] = getlocal("app.title");
$page['show_small_login'] = true;
$page['fixedwrap'] = true;

function tpl_content() { 
	global $page, $webimroot, $errors;
	
	if($page['isdone']) {
?>
<div id="loginpane">
	<div class="header">	
		<h2><?php echo getlocal("resetpwd.changed.title") ?></h2>
	</div>

	<div class="fieldForm">
		<?php echo getlocal("resetpwd.changed") ?>
		<br/>
		<br/>
		<a href="login.php"><?php echo getlocal("resetpwd.login") ?></a>
	</div>
</div>	
	
<?php 		
	} else {
?>

<form name="resetForm" method="post" action="<?php echo $webimroot ?>/operator/resetpwd.php">
<input type="hidden" name="id" value="<?php echo $page['id'] ?>"/>
<input type="hidden" name="token" value="<?php echo $page['token'] ?>"/>

	<div id="loginpane">

	<div class="header">	
		<h2><?php echo getlocal("resetpwd.title") ?></h2>
	</div>

	<div class="fieldForm">
	
		<?php echo getlocal("resetpwd.intro") ?><br/><br/>

<?php 
require_once('inc_errors.php');
?>
	
<?php if($page['showform']) { ?>
		<div class="field">
			<div class="fleftlabel"><?php echo getlocal('form.field.password') ?></div>
			<div class="fvalue">
				<input type="password" name="password" size="25" value="" class="formauth"/>
			</div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="fleftlabel"><?php echo getlocal('form.field.password_confirm') ?></div>
			<div class="fvalue">
				<input type="password" name="passwordConfirm" size="25" value="" class="formauth"/>
			</div>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<table class="submitbutton"><tr>
				<td><a href="javascript:resetForm.submit();">
					<img src='<?php echo $webimroot ?>/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
				<td class="submit"><a href="javascript:resetForm.submit();">
					<?php echo getlocal("resetpwd.submit") ?></a></td>
				<td><a href="javascript:resetForm.submit();">
					<img src='<?php echo $webimroot ?>/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
			</tr></table>

			<div class="links">
				<a href="login.php"><?php echo getlocal("restore.back_to_login") ?></a>
			</div>
		</div>
<?php } else { ?>
		<a href="login.php"><?php echo getlocal("restore.back_to_login") ?></a>
<?php } ?>
	</div>

	</div>		
</form>

<?php 
	}
} /* content */

require_once('inc_main.php');
?>