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
$page['title'] = getlocal("page.gen_button.title");
$page['menuid'] = "getcode";

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("page.gen_button.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>

<form name="buttonCodeForm" method="get" action="<?php echo $webimroot ?>/operator/getcode.php">
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_image") ?></div>
			<div class="fvaluenodesc">
				<select name="image" onchange="this.form.submit();"><?php foreach($page['availableImages'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("image") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_locale") ?></div>
			<div class="fvaluenodesc">
				<select name="lang" onchange="this.form.submit();"><?php foreach($page['availableLocales'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("lang") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
		</div>

<?php if($page['showgroups']) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_group") ?></div>
			<div class="fvaluenodesc">
				<select name="group" onchange="this.form.submit();"><?php foreach($page['groups'] as $k) { echo "<option value=\"".$k['groupid']."\"".($k['groupid'] == form_value("group") ? " selected=\"selected\"" : "").">".$k['vclocalname']."</option>"; } ?></select>
			</div>
		</div>
<?php } ?>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_style") ?></div>
			<div class="fvaluenodesc">
				<select name="style" onchange="this.form.submit();"><?php foreach($page['availableStyles'] as $k => $v) { echo "<option value=\"".$k."\"".($k == form_value("style") ? " selected=\"selected\"" : "").">".$v."</option>"; } ?></select>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.include_site_name") ?></div>
			<div class="fvaluenodesc">
				<input type="checkbox" name="hostname" value="on"<?php echo form_value_cb('hostname') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
			</div>
		</div>

<?php if( $page['formhostname'] ) { ?>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.secure_links") ?></div>
			<div class="fvaluenodesc">
				<input type="checkbox" name="secure" value="on"<?php echo form_value_cb('secure') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
			</div>
		</div>
<?php } ?>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.code") ?></div>
			<div class="fvaluewithta">
				<textarea cols="44" rows="15"><?php echo htmlspecialchars($page['buttonCode']) ?></textarea>
			</div>
			<div class="fdescr"><?php echo getlocal("page.gen_button.code.description") ?></div>
			<br clear="left"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.sample") ?></div>
			<div class="fvaluenodesc">
				<?php echo $page['buttonCode'] ?>
			</div>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>