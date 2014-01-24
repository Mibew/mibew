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
$page['title'] = getlocal("page.translate.title");
$page['menuid'] = "translate";

function tpl_content() { global $page, $mibewroot;
?>

<?php echo getlocal("page.translate.descr") ?>
<br />
<br />

<form name="translateForm" method="get" action="<?php echo $mibewroot ?>/operator/translate.php">
	
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="packedFormField">
		<?php echo getlocal("translate.direction") ?><br/>
		<select name="source" onchange="this.form.submit();"><?php 
			foreach($page['availableLocales'] as $k) { 
				echo "<option value=\"" . safe_htmlspecialchars($k["id"]) . "\"".($k["id"] == form_value("source") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k["name"]) . "</option>";
			} ?></select>
		=&gt;
		<select name="target" onchange="this.form.submit();"><?php
			foreach($page['availableLocales'] as $k) { 
				echo "<option value=\"" . safe_htmlspecialchars($k["id"]) . "\"".($k["id"] == form_value("target") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k["name"]) . "</option>";
			} ?></select>
	</div>

	<div class="packedFormField">
		<?php echo getlocal("translate.sort") ?><br/>
		<select name="sort" onchange="this.form.submit();"><?php
			foreach($page['availableOrders'] as $k) {
				echo "<option value=\"" . safe_htmlspecialchars($k["id"]) . "\"".($k["id"] == form_value("sort") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k["name"]) . "</option>";
			} ?></select>
	</div>

	<div class="packedFormField">
		<?php echo getlocal("translate.show") ?><br/>
		<select name="show" onchange="this.form.submit();"><?php 
			foreach($page['showOptions'] as $k) { 
				echo "<option value=\"" . safe_htmlspecialchars($k["id"]) . "\"".($k["id"] == form_value("show") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k["name"]) . "</option>";
			} ?></select>
	</div>

	<br clear="all"/>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>
<br/>


<?php
if( $page['pagination'] ) {
	if( $page['pagination.items'] ) {
		echo generate_pagination($page['pagination'], false);
	}
?>

<table class="translate">
<thead>
	<tr class="header"><th>
		Key
	</th><th>
		<?php echo safe_htmlspecialchars(topage($page['title1'])) ?>
	</th><th>
		<?php echo safe_htmlspecialchars(topage($page['title2'])) ?>
	</th></tr>
</thead>
<tbody>
<?php
if( $page['pagination.items'] ) {
	foreach( $page['pagination.items'] as $localstr ) { ?>
	<tr>
		<td>
			<a href="<?php echo $mibewroot ?>/operator/translate.php?source=<?php echo urlencode($page['lang1']) ?>&amp;target=<?php echo urlencode($page['lang2']) ?>&amp;key=<?php echo urlencode($localstr['id']) ?>" target="_blank" onclick="this.newWindow = window.open('<?php echo $mibewroot ?>/operator/translate.php?source=<?php echo urlencode($page['lang1']) ?>&amp;target=<?php echo urlencode($page['lang2']) ?>&amp;key=<?php echo urlencode($localstr['id']) ?>', '', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><?php echo safe_htmlspecialchars(topage($localstr['id'])) ?></a>
		</td>
		<td>
			<?php echo topage($localstr['l1']) ?>
		</td>
		<td>
			<?php echo topage($localstr['l2']) ?>
		</td>
	</tr>
<?php
	}
} else {
?>
	<tr>
	<td colspan="3">
		<?php echo getlocal("tag.pagination.no_items") ?>
	</td>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php
	if( $page['pagination.items'] ) { 
		echo "<br/>";
		echo generate_pagination($page['pagination']);
	}
}
?>

<?php
} /* content */

require_once('inc_main.php');
?>