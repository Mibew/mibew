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
$page['title'] = getlocal("page_login.title");
$page['headertitle'] = getlocal("app.title");
$page['show_small_login'] = false;
$page['fixedwrap'] = true;

function tpl_content() { global $page, $mibewroot, $errors;
?>

<div id="loginintro">
<p><?php echo getlocal("app.descr")?></p>
</div>

<form name="loginForm" method="post" action="<?php echo $mibewroot ?>/operator/login.php">
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
				<input type="password" name="password" size="25" value="" class="formauth" autocomplete="off"/>
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
			<input type="image" name="login" src="<?php echo $mibewroot . safe_htmlspecialchars(getlocal("image.button.login")) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("button.enter")) ?>"/>

			<div class="links">
				<a href="<?php echo $mibewroot ?>/operator/restore.php"><?php echo getlocal("restore.pwd.message") ?></a><br/>
			</div>
		</div>

	</div>

	</div>
</form>

<?php
} /* content */

require_once('inc_main.php');
?>