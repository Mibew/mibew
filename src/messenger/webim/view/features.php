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

require_once("inc_menu.php");
$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page_settings.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("features.saved") ?></div>
<?php } ?>

<form name="features" method="post" action="<?php echo $webimroot ?>/operator/features.php">
<input type="hidden" name="sent" value="true"/>
	<div>
<?php if($page['tabs']) { ?>
	<ul class="tabs">

<?php foreach($page['tabs'] as $k => $v) { if($v) { ?>
	<li><a href="<?php echo $v ?>"><?php echo $k ?></a></li>
<?php } else { ?>
	<li class="active"><a href="#"><?php echo $k ?></a></li><?php }} ?>
	</ul>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.usercanchangename') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="usercanchangename" value="on"<?php echo form_value_cb('usercanchangename') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.usercanchangename.description') ?></div>
			<br clear="left"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enableban') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enableban" value="on"<?php echo form_value_cb('enableban') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enableban.description') ?></div>
			<br clear="left"/>
		</div>
		 
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablessl') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablessl" value="on"<?php echo form_value_cb('enablessl') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablessl.description') ?></div>
			<br clear="left"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enabledepartments') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enabledepartments" value="on"<?php echo form_value_cb('enabledepartments') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enabledepartments.description') ?></div>
			<br clear="left"/>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>

	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>