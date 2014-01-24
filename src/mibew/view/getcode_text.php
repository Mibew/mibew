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

require_once("inc_menu.php");
require_once("inc_tabbar.php");

$page['title'] = getlocal("page.gen_button.title");
$page['menuid'] = "getcode";

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("page.gen_button.intro") ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>

<form name="buttonCodeForm" method="get" action="<?php echo $mibewroot ?>/operator/gettextcode.php">
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">

		<div class="fieldinrow">
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_locale") ?></div>
			<div class="fvaluenodesc">
				<select name="lang" onchange="this.form.submit();"><?php foreach($page['availableLocales'] as $k) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("lang") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k) . "</option>"; } ?></select>
			</div>
		</div>

<?php if($page['showgroups']) { ?>
		<div class="fieldinrow">
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_group") ?></div>
			<div class="fvaluenodesc">
				<select name="group" onchange="this.form.submit();"><?php foreach($page['groups'] as $k) { echo "<option value=\"" . safe_htmlspecialchars($k['groupid']) . "\"".($k['groupid'] == form_value("group") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k['vclocalname']) . "</option>"; } ?></select>
			</div>
		</div>
<?php } ?>
		<br clear="all"/>

		<div class="fieldinrow">
			<div class="flabel"><?php echo getlocal("page.gen_button.choose_style") ?></div>
			<div class="fvaluenodesc">
				<select name="style" onchange="this.form.submit();"><?php foreach($page['availableStyles'] as $k => $v) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("style") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($v) . "</option>"; } ?></select>
			</div>
		</div>
		<br clear="all"/>

		<div class="fieldinrow">
			<div class="flabel"><?php echo getlocal("page.gen_button.include_site_name") ?></div>
			<div class="fvaluenodesc">
				<input type="checkbox" name="hostname" value="on"<?php echo form_value_cb('hostname') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
			</div>
		</div>

<?php if( $page['formhostname'] ) { ?>

		<div class="fieldinrow">
			<div class="flabel"><?php echo getlocal("page.gen_button.secure_links") ?></div>
			<div class="fvaluenodesc">
				<input type="checkbox" name="secure" value="on"<?php echo form_value_cb('secure') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
			</div>
		</div>
<?php } ?>
		<br clear="all"/>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.modsecurity") ?></div>
			<div class="fvaluenodesc">
				<input type="checkbox" name="modsecurity" value="on"<?php echo form_value_cb('modsecurity') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal("page.gen_button.code") ?></div>
			<div class="fvaluewithta" dir="ltr">
				<textarea cols="44" rows="15"><?php echo safe_htmlspecialchars($page['buttonCode']) ?></textarea>
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
	</div>
</form>

<?php
} /* content */

require_once('inc_main.php');
?>