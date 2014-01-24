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
$page['title'] = getlocal("restore.title");
$page['headertitle'] = getlocal("app.title");
$page['show_small_login'] = true;
$page['fixedwrap'] = true;

function tpl_content() {
	global $page, $mibewroot, $errors;
	
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
		<a href="<?php echo $mibewroot ?>/operator/login.php"><?php echo getlocal("restore.back_to_login") ?></a>
	</div>
</div>

<?php
	} else {
?>

<form name="restoreForm" method="post" action="<?php echo $mibewroot ?>/operator/restore.php">
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
				<td><a href="javascript:document.restoreForm.submit();">
					<img src="<?php echo $mibewroot ?>/images/submit.gif" width="40" height="35" border="0" alt="" /></a></td>
				<td class="submit"><a href="javascript:document.restoreForm.submit();">
					<?php echo getlocal("restore.submit") ?></a></td>
				<td><a href="javascript:document.restoreForm.submit();">
					<img src="<?php echo $mibewroot ?>/images/submitrest.gif" width="10" height="35" border="0" alt="" /></a></td>
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