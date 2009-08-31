<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
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
$page['title'] = getlocal("restore.title");
$page['headertitle'] = getlocal("app.title");
$page['show_small_login'] = true;
$page['fixedwrap'] = true;

function tpl_content() { 
	global $page, $webimroot, $errors;
	
	if($page['isdone']) {
?>
<div id="loginpane">
	<div class="header">	
		<h2><?php echo getlocal("restore.sent.title") ?></h2>
	</div>

	<div class="fieldForm">
		<?php echo getlocal("restore.sent") ?>
		<br/>
		<br/>
		<a href="login.php"><?php echo getlocal("restore.back_to_login") ?></a>
	</div>
</div>	
	
<?php 		
	} else {
?>

<form name="restoreForm" method="post" action="<?php echo $webimroot ?>/operator/restore.php">
	<div id="loginpane">

	<div class="header">	
		<h2><?php echo getlocal("restore.title") ?></h2>
	</div>

	<div class="fieldForm">
	
		<?php echo getlocal("restore.intro") ?><br/><br/>

<?php 
require_once('inc_errors.php');
?>
	
		<div class="field">
			<div class="fleftlabel"><?php echo getlocal("restore.emailorlogin") ?></div>
			<div class="fvalue">
				<input type="text" name="loginoremail" size="25" value="<?php echo form_value('loginoremail') ?>" class="formauth"/>
			</div>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<table class="submitbutton"><tr>
				<td><a href="javascript:restoreForm.submit();">
					<img src='<?php echo $webimroot ?>/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
				<td class="submit"><a href="javascript:restoreForm.submit();">
					<?php echo getlocal("restore.submit") ?></a></td>
				<td><a href="javascript:restoreForm.submit();">
					<img src='<?php echo $webimroot ?>/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
			</tr></table>

			<div class="links">
				<a href="login.php"><?php echo getlocal("restore.back_to_login") ?></a>
			</div>
		</div>

	</div>

	</div>		
</form>

<?php 
	}
} /* content */

require_once('inc_main.php');
?>