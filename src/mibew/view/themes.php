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

$page['title'] = getlocal("page.preview.title");
$page['menuid'] = "settings";

function tpl_content() { global $page, $mibewroot;
?>

<?php echo getlocal("page.preview.intro") ?>
<br />
<br />

<form name="preview" method="get" action="<?php echo $mibewroot ?>/operator/themes.php">
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.preview.choose") ?></div>
			<div class="fvaluenodesc">
				<select name="preview" onchange="this.form.submit();"><?php foreach($page['availablePreviews'] as $k) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("preview") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k) . "</option>"; } ?></select>
			</div>
		</div>
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.preview.choosetpl") ?></div>
			<div class="fvaluenodesc">
				<select name="template" onchange="this.form.submit();"><?php foreach($page['availableTemplates'] as $k) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("template") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k) . "</option>"; } ?></select>
			</div>
		</div>
<?php if( $page['canshowerrors'] ) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.preview.showerr") ?></div>
			<div class="fvaluenodesc">
				<input type="checkbox" name="showerr" value="on"<?php echo form_value_cb('showerr') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
			</div>
		</div>
<?php } ?>
<?php foreach( $page['previewList'] as $pp ) { ?>
		<div class="field">
			<div class="flabel">
			<?php echo safe_htmlspecialchars($pp['label']) ?>
			<a href="<?php echo safe_htmlspecialchars($page['showlink']) ?><?php echo safe_htmlspecialchars($pp['id']) ?>" target="_blank" title="<?php echo safe_htmlspecialchars(getlocal("page.preview.in_separate_window")) ?>" onclick="this.newWindow = window.open('<?php echo safe_htmlspecialchars($page['showlink']) ?><?php echo safe_htmlspecialchars($pp['id']) ?>', '<?php echo safe_htmlspecialchars($pp['id']) ?>', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=<?php echo safe_htmlspecialchars($pp['w']) ?>,height=<?php echo safe_htmlspecialchars($pp['h']) ?>,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><?php echo getlocal("page.preview.link") ?></a>
			</div>
			<div class="fvalueframe">
			<iframe id="sample<?php echo safe_htmlspecialchars($pp['id']) ?>" width="<?php echo safe_htmlspecialchars($pp['w']) ?>" height="<?php echo safe_htmlspecialchars($pp['h']) ?>" src="<?php echo $page['showlink'] ?><?php echo safe_htmlspecialchars($pp['id']) ?>" frameborder="0" scrolling="no">
				<?php echo getlocal("page.preview.no_iframes") ?>
			</iframe>
			</div>
		</div>
<?php } ?>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>
</form>

<?php
} /* content */

require_once('inc_main.php');
?>