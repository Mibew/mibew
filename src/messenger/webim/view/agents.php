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
$page['title'] = getlocal("page_agents.title");
$page['menuid'] = "operators";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page_agents.intro") ?>
<br />
<br />

<div class="tabletool">
	<img src='<?php echo $webimroot ?>/images/buttons/createagent.gif' border="0" alt="" />
	<a href='<?php echo $webimroot ?>/operator/operator.php' title="<?php echo getlocal("page_agents.new_agent") ?>">
		<?php echo getlocal("page_agents.new_agent") ?>
	</a>
</div>
<br clear="all"/>


<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("page_agents.login") ?>
</th><th>
	<?php echo getlocal("page_agents.agent_name") ?>
</th><th>
	<?php echo getlocal("page_agents.agent_commonname") ?>
</th>
</tr>
</thead>
<tbody>
<?php foreach( $page['allowedAgents'] as $a ) { ?>
<tr>
	<td class="notlast">
   		<a href="<?php echo $webimroot ?>/operator/operator.php?op=<?php echo $a['operatorid'] ?>" class="man">
   			<?php echo htmlspecialchars(topage($a['vclogin'])) ?>
   		</a>
	</td>
	<td class="notlast">
   		<?php echo htmlspecialchars(topage($a['vclocalename'])) ?>
	</td>
	<td>
   		<?php echo htmlspecialchars(topage($a['vccommonname'])) ?>
	</td>
</tr>
<?php } ?>
</tbody>
</table>

<?php 
} /* content */

require_once('inc_main.php');
?>
