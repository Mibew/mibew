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
$page['title'] = getlocal("page.notifications.title");
$page['menuid'] = "notifications";

function shorten($text,$len) {
	if(strlen($text) > $len)
		return substr($text,0,$len)."..";
	return $text;
}

function tpl_header() { global $page, $mibewroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/jquery-1.4.2.min.js"></script>
<?php
}

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("page.notifications.intro") ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>

<form name="notifyFilterForm" method="get" action="<?php echo $mibewroot ?>/operator/notifications.php">

	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="packedFormField">
		<?php echo getlocal("notifications.kind") ?><br/>
		<select name="kind" onchange="this.form.submit();"><?php
			foreach($page['allkinds'] as $k) {
				echo "<option value=\"".safe_htmlspecialchars($k)."\"".($k == form_value("kind") ? " selected=\"selected\"" : "").">".getlocal("notifications.kind.".($k ? $k : "all"))."</option>";
			} ?></select>
	</div>

	<div class="packedFormField">
		<?php echo getlocal("notifications.locale") ?><br/>
		<select name="lang" onchange="this.form.submit();"><?php
			foreach($page['locales'] as $k) {
				echo "<option value=\"" . safe_htmlspecialchars($k["id"]) . "\"".($k["id"] == form_value("lang") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k["name"]) . "</option>";
			} ?></select>
	</div>

	<br clear="all"/>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>
<br/>

<?php if( $page['pagination'] ) { ?>

<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("notifications.head.to") ?>
</th><th>
	<?php echo getlocal("notifications.head.subj") ?>
</th><th>
	<?php echo getlocal("notifications.head.msg") ?>
</th><th>
	<?php echo getlocal("notifications.head.time") ?>
</th>
</tr>
</thead>
<tbody>
<?php
if( $page['pagination.items'] ) {
	foreach( $page['pagination.items'] as $b ) { ?>
	<tr>
	<td class="notlast">
		<a href="<?php echo $mibewroot ?>/operator/notification.php?id=<?php echo urlencode($b['id']) ?>" target="_blank" onclick="this.newWindow = window.open('<?php echo $mibewroot ?>/operator/notification.php?id=<?php echo urlencode($b['id']) ?>', '', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=720,height=520,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;" class="<?php echo $b['vckind'] == 'xmpp' ? 'xmpp' : 'mail' ?>">
			<?php echo safe_htmlspecialchars(shorten(topage($b['vcto']),30)) ?>
		</a>
	</td>
	<td class="notlast">
		<?php echo safe_htmlspecialchars(shorten(topage($b['vcsubject']),30)) ?>
	</td>
	<td class="notlast">
		<?php echo safe_htmlspecialchars(shorten(topage($b['tmessage']),30)) ?>
	</td>
	<td>
		<?php echo date_to_text($b['created']) ?>
	</td>
	</tr>
<?php
	}
} else {
?>
	<tr>
	<td colspan="4">
		<?php echo getlocal("tag.pagination.no_items.elements") ?>
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