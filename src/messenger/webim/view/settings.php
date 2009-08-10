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

require_once("inc_menu.php");
$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("page_settings.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("settings.saved") ?></div>
<?php } ?>

<form name="settings" method="post" action="<?php echo $webimroot ?>/operator/settings.php">

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
			<div class="flabel"><?php echo getlocal('settings.email') ?></div>
			<div class="fvalue">
				<input type="text" name="email" size="40" value="<?php echo form_value('email') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.email.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.company.title') ?></div>
			<div class="fvalue">
				<input type="text" name="title" size="40" value="<?php echo form_value('title') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.company.title.description') ?></div>
			<br clear="all"/>
		</div>
		 
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.chat.title') ?></div>
			<div class="fvalue">
				<input type="text" name="chattitle" size="40" value="<?php echo form_value('chattitle') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.chat.title.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.logo') ?></div>
			<div class="fvalue">
				<input type="text" name="logo" size="40" value="<?php echo form_value('logo') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.logo.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.host') ?></div>
			<div class="fvalue">
				<input type="text" name="hosturl" size="40" value="<?php echo form_value('hosturl') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.host.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.geolink') ?></div>
			<div class="fvalue">
				<input type="text" name="geolink" size="40" value="<?php echo form_value('geolink') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.geolink.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.geolinkparams') ?></div>
			<div class="fvalue">
				<input type="text" name="geolinkparams" size="40" value="<?php echo form_value('geolinkparams') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.geolinkparams.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.usernamepattern') ?></div>
			<div class="fvalue">
				<input type="text" name="usernamepattern" size="40" value="<?php echo form_value('usernamepattern') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.usernamepattern.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.chatstyle') ?></div>
			<div class="fvalue">
				<select name="chatstyle" ><?php foreach($page['availableStyles'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("chatstyle") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.chatstyle.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.sendmessagekey') ?></div>
			<div class="fvaluenodesc">
				<label>
					<input type="radio" name="sendmessagekey" value="enter" <?php echo form_value("sendmessagekey") == "enter" ? " checked=\"checked\"" : ""?>/>Enter</label>
				<label>	
					<input type="radio" name="sendmessagekey" value="center" <?php echo form_value("sendmessagekey") == "center" ? " checked=\"checked\"" : ""?>/>Ctrl-Enter</label>
			</div>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>

	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
	
	<div class="asterisk">
		<?php echo getlocal("common.asterisk_explanation") ?>
	</div>

</form>

<?php 
} /* content */

require_once('inc_main.php');
?>