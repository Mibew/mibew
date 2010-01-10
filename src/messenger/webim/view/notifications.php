<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2010 Mibew Messenger Community
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
$page['title'] = getlocal("page.notifications.title");
$page['menuid'] = "notifications";

function shorten($text,$len) {
	if(strlen($text) > $len)
		return substr($text,0,$len)."..";
	return $text;
}

function tpl_header() { global $page, $webimroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/jquery-1.3.2.min.js"></script>
<?php
}

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("page.notifications.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>

<form name="notifyFilterForm" method="get" action="<?php echo $webimroot ?>/operator/notifications.php">
	
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="packedFormField">
		<?php echo getlocal("notifications.kind") ?><br/>
		<select name="kind" onchange="this.form.submit();"><?php 
			foreach($page['allkinds'] as $k) { 
				echo "<option value=\"".$k."\"".($k == form_value("kind") ? " selected=\"selected\"" : "").">".getlocal("notifications.kind.".($k ? $k : "all"))."</option>";
			} ?></select>
	</div>

	<div class="packedFormField">
		<?php echo getlocal("notifications.locale") ?><br/>
		<select name="lang" onchange="this.form.submit();"><?php
			foreach($page['locales'] as $k) {
				echo "<option value=\"".$k["id"]."\"".($k["id"] == form_value("lang") ? " selected=\"selected\"" : "").">".$k["name"]."</option>";
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
		<a href="<?php echo $webimroot ?>/operator/notification.php?id=<?php echo $b['id'] ?>" target="_blank" onclick="this.newWindow = window.open('<?php echo $webimroot ?>/operator/notification.php?id=<?php echo $b['id'] ?>', '', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=720,height=520,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;" class="<?php echo $b['vckind'] == 'xmpp' ? 'xmpp' : 'mail' ?>">
   			<?php echo htmlspecialchars(shorten(topage($b['vcto']),30)) ?>
   		</a>
	</td>
	<td class="notlast">
		<?php echo htmlspecialchars(shorten(topage($b['vcsubject']),30)) ?>
	</td>
	<td class="notlast">
		<?php echo htmlspecialchars(shorten(topage($b['tmessage']),30)) ?>
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