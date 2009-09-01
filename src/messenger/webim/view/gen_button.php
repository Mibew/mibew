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
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_locale") ?></div>
			<div class="fvaluenodesc">
				<select name="lang" onchange="this.form.submit();"><?php foreach($page['availableLocales'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("lang") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_image") ?></div>
			<div class="fvaluenodesc">
				<select name="i" onchange="this.form.submit();"><?php foreach($page['availableImages'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("image") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
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
			<div class="flabel"><?php echo getlocal("page.gen_button.modsecurity") ?></div>
			<div class="fvaluenodesc">
				<input type="checkbox" name="modsecurity" value="on"<?php echo form_value_cb('modsecurity') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.code") ?></div>
			<div class="fvaluewithta" dir="ltr">
				<textarea cols="44" rows="15"><?php echo htmlspecialchars($page['buttonCode']) ?></textarea>
			</div>
			<div class="fdescr"><?php echo getlocal("page.gen_button.code.description") ?></div>
			<br clear="all"/>
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