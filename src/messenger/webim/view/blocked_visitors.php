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
$page['title'] = getlocal("page_bans.title");
$page['menuid'] = "blocked";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page_ban.intro") ?>
<br />
<br />

<div class="tabletool">
	<img src="<?php echo $webimroot ?>/images/buttons/createban.gif" border="0" alt=""/>
	<a href="<?php echo $webimroot ?>/operator/ban.php" title="<?php echo getlocal("page_bans.add") ?>">
		<?php echo getlocal("page_bans.add") ?>
	</a>
</div>
<br clear="all"/>

<?php if( $page['pagination'] ) { ?>

<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("form.field.address") ?>
</th><th>
	<?php echo getlocal("page_bans.to") ?>
</th><th>
	<?php echo getlocal("form.field.ban_comment") ?>
</th><th>
</th>
</tr>
</thead>
<tbody>
<?php 
if( $page['pagination.items'] ) {
	foreach( $page['pagination.items'] as $b ) { ?>
	<tr>
	<td class="notlast">
		<a href="ban.php?id=<?php echo $b['banid'] ?>" class="man">
		   	<?php echo htmlspecialchars($b['address']) ?>
	   	</a>
	</td>
	<td class="notlast">
   		<?php echo strftime("%B, %d %Y %H:%M:%S", $b['till']) ?>
	</td>
	<td>
<?php 
	if( strlen(topage($b['comment'])) > 30 ) { 
		echo htmlspecialchars(substr(topage($b['comment']),0,30));
	} else {
		echo htmlspecialchars(topage($b['comment']));
	} 
?>
	</td>
	<td>
		<a href="<?php echo $webimroot ?>/operator/blocked.php?act=del&amp;id=<?php echo $b['banid'] ?>">
			remove
		</a>
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