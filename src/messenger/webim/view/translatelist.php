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
$page['title'] = getlocal("page.translate.title");
$page['menuid'] = "translate";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page.translate.descr") ?>
<br />
<br />

<form name="translateForm" method="get" action="<?php echo $webimroot ?>/operator/translate.php">
	
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="packedFormField">
		<?php echo getlocal("translate.direction") ?><br/>
		<select name="source" onchange="this.form.submit();"><?php 
			foreach($page['availableLocales'] as $k) { 
				echo "<option value=\"".$k["id"]."\"".($k["id"] == form_value("source") ? " selected=\"selected\"" : "").">".$k["name"]."</option>";
			} ?></select>
		=&gt;
		<select name="target" onchange="this.form.submit();"><?php 
			foreach($page['availableLocales'] as $k) { 
				echo "<option value=\"".$k["id"]."\"".($k["id"] == form_value("target") ? " selected=\"selected\"" : "").">".$k["name"]."</option>";
			} ?></select>
	</div>
	
	<div class="packedFormField">
		<?php echo getlocal("translate.sort") ?><br/>
		<select name="sort" onchange="this.form.submit();"><?php
			foreach($page['availableOrders'] as $k) {
				echo "<option value=\"".$k["id"]."\"".($k["id"] == form_value("sort") ? " selected=\"selected\"" : "").">".$k["name"]."</option>";
			} ?></select>
	</div>
	
	<div class="packedFormField">
		<?php echo getlocal("translate.show") ?><br/>
		<select name="show" onchange="this.form.submit();"><?php 
			foreach($page['showOptions'] as $k) { 
				echo "<option value=\"".$k["id"]."\"".($k["id"] == form_value("show") ? " selected=\"selected\"" : "").">".$k["name"]."</option>";
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
		<?php echo topage($page['title1']) ?>
	</th><th>
		<?php echo topage($page['title2']) ?>
	</th></tr>
</thead>
<tbody>
<?php 
if( $page['pagination.items'] ) {	
	foreach( $page['pagination.items'] as $localstr ) { ?>
	<tr>
		<td>
			<a href="<?php echo $webimroot ?>/operator/translate.php?source=<?php echo $page['lang1'] ?>&amp;target=<?php echo $page['lang2'] ?>&amp;key=<?php echo $localstr['id'] ?>" target="_blank" onclick="this.newWindow = window.open('<?php echo $webimroot ?>/operator/translate.php?source=<?php echo $page['lang1'] ?>&amp;target=<?php echo $page['lang2'] ?>&amp;key=<?php echo $localstr['id'] ?>', '', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><?php echo topage($localstr['id']) ?></a>
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