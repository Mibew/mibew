<?php
/*
 * Copyright 2005-2013 the original author or authors.
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
				<select name="preview" onchange="this.form.submit();"><?php foreach($page['availablePreviews'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("preview") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
		</div>
<?php foreach($page['screenshotsList'] as $screenshot) { ?>
		<div class="field">
			<div class="flabel">
				<?php echo($screenshot['description']); ?>
			</div>
			<div class="fvalueframe">
				<img class="screenshot" alt="<?php echo($screenshot['name']); ?>" src="<?php echo($screenshot['file']); ?>" />
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