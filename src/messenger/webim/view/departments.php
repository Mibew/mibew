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
$page['title'] = getlocal("page.departments.title");
$page['menuid'] = "departments";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page.departments.intro") ?>
<br />
<br />

<div class="tabletool">
	<img src='<?php echo $webimroot ?>/images/buttons/createdep.gif' border="0" alt="" />
	<a href='<?php echo $webimroot ?>/operator/department.php' title="<?php echo getlocal("page.departments.new") ?>">
		<?php echo getlocal("page.departments.new") ?>
	</a>
</div>
<br clear="all"/>


<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("form.field.depname") ?>
</th><th>
	<?php echo getlocal("form.field.depdesc") ?>
</th><th>
	<?php echo getlocal("page.department.membersnum") ?>
</th><th>
</th>
</tr>
</thead>
<tbody>
<?php
if(count($page['departments']) > 0) { 
	foreach( $page['departments'] as $dep ) { ?>
<tr>
	<td class="notlast">
   		<a href="<?php echo $webimroot ?>/operator/department.php?dep=<?php echo $dep['departmentid'] ?>" class="man">
   			<?php echo htmlspecialchars(topage($dep['vclocalname'])) ?>
   		</a>
	</td>
	<td class="notlast">
   		<?php echo $dep['vclocaldescription'] ? htmlspecialchars(topage($dep['vclocaldescription'])) : "&lt;none&gt;" ?>
	</td>
	<td>
   		<?php echo htmlspecialchars(topage($dep['inumofagents'])) ?>
	</td>
	<td>
		<a href="<?php echo $webimroot ?>/operator/departments.php?act=del&amp;dep=<?php echo $dep['departmentid'] ?>">
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