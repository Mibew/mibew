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
		</div>

	</div>

	</div>		
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>