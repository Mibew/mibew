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
$page['title'] = getlocal("page.groups.title");
$page['menuid'] = "groups";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page.groups.intro") ?>
<br />
<br />

<div class="tabletool">
	<img src='<?php echo $webimroot ?>/images/buttons/createdep.gif' border="0" alt="" />
	<a href='<?php echo $webimroot ?>/operator/group.php' title="<?php echo getlocal("page.groups.new") ?>">
		<?php echo getlocal("page.groups.new") ?>
	</a>
</div>
<br clear="all"/>


<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("form.field.groupname") ?>
</th><th>
	<?php echo getlocal("form.field.groupdesc") ?>
</th><th>
	<?php echo getlocal("page.group.membersnum") ?>
</th><th>
</th>
</tr>
</thead>
<tbody>
<?php
if(count($page['groups']) > 0) { 
	foreach( $page['groups'] as $grp ) { ?>
<tr>
	<td class="notlast">
   		<a href="<?php echo $webimroot ?>/operator/group.php?gid=<?php echo $grp['groupid'] ?>" class="man">
   			<?php echo htmlspecialchars(topage($grp['vclocalname'])) ?>
   		</a>
	</td>
	<td class="notlast">
   		<?php echo $grp['vclocaldescription'] ? htmlspecialchars(topage($grp['vclocaldescription'])) : "&lt;none&gt;" ?>
	</td>
	<td>
   		<a href="<?php echo $webimroot ?>/operator/groupmembers.php?gid=<?php echo $grp['groupid'] ?>">
	   		<?php echo htmlspecialchars(topage($grp['inumofagents'])) ?>
   		</a>
	</td>
	<td>
		<a href="<?php echo $webimroot ?>/operator/groups.php?act=del&amp;gid=<?php echo $grp['groupid'] ?>">
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
} /* content */

require_once('inc_main.php');
?>