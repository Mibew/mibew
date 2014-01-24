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

if(isset($page) && isset($page['localeLinks'])) {
	require_once('inc_locales.php');
}
$page['title'] = getlocal("resetpwd.title");
$page['headertitle'] = getlocal("app.title");
$page['show_small_login'] = true;
$page['fixedwrap'] = true;

function tpl_content() {
	global $page, $mibewroot, $errors;
	
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
		<a href="<?php echo $mibewroot ?>/operator/login.php?login=<?php echo urlencode($page['loginname']) ?>"><?php echo getlocal("resetpwd.login") ?></a>
	</div>
</div>

<?php
	} else {
?>

<form name="resetForm" method="post" action="<?php echo $mibewroot ?>/operator/resetpwd.php">
<input type="hidden" name="id" value="<?php echo safe_htmlspecialchars($page['id']) ?>"/>
<input type="hidden" name="token" value="<?php echo safe_htmlspecialchars($page['token']) ?>"/>

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
				<input type="password" name="password" size="25" value="" class="formauth" autocomplete="off"/>
			</div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="fleftlabel"><?php echo getlocal('form.field.password_confirm') ?></div>
			<div class="fvalue">
				<input type="password" name="passwordConfirm" size="25" value="" class="formauth" autocomplete="off"/>
			</div>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<table class="submitbutton"><tr>
				<td><a href="javascript:document.resetForm.submit();">
					<img src="<?php echo $mibewroot ?>/images/submit.gif" width="40" height="35" border="0" alt="" /></a></td>
				<td class="submit"><a href="javascript:document.resetForm.submit();">
					<?php echo getlocal("resetpwd.submit") ?></a></td>
				<td><a href="javascript:document.resetForm.submit();">
					<img src="<?php echo $mibewroot ?>/images/submitrest.gif" width="10" height="35" border="0" alt="" /></a></td>
			</tr></table>

			<div class="links">
				<a href="<?php echo $mibewroot ?>/operator/login.php"><?php echo getlocal("restore.back_to_login") ?></a>
			</div>
		</div>
<?php } else { ?>
		<a href="<?php echo $mibewroot ?>/operator/login.php"><?php echo getlocal("restore.back_to_login") ?></a>
<?php } ?>
	</div>

	</div>
</form>

<?php
	}
} /* content */

require_once('inc_main.php');
?>