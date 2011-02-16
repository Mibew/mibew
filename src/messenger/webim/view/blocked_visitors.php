<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
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
$page['title'] = getlocal("page_bans.title");
$page['menuid'] = "blocked";

function tpl_header() { global $page, $webimroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/jquery-1.3.2.min.js"></script>
<?php
}

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("page_ban.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>

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
		<a href="ban.php?id=<?php echo $b['banid'] ?>" class="man" id="ti<?php echo $b['banid'] ?>">
		   	<?php echo htmlspecialchars($b['address']) ?>
	   	</a>
	</td>
	<td class="notlast">
   		<?php echo date_to_text($b['till']) ?>
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
		<a class="removelink" id="i<?php echo $b['banid'] ?>" href="<?php echo $webimroot ?>/operator/blocked.php?act=del&amp;id=<?php echo $b['banid'] ?>">
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
<script type="text/javascript" language="javascript"><!--
$('a.removelink').click(function(){
	var addr = $("#t"+this.id).text();
	return confirm("<?php echo getlocalforJS("page_bans.confirm", array('"+$.trim(addr)+"')) ?>");
});
//--></script>

<?php 
} /* content */

require_once('inc_main.php');
?>