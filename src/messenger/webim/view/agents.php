<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

function tpl_header() { global $page, $webimroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/jquery-1.3.2.min.js"></script>
<?php
}

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("page_agents.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>

<?php if($page['canmodify']) { ?>
<div class="tabletool">
	<img src='<?php echo $webimroot ?>/images/buttons/createagent.gif' border="0" alt="" />
	<a href='<?php echo $webimroot ?>/operator/operator.php' title="<?php echo getlocal("page_agents.new_agent") ?>">
		<?php echo getlocal("page_agents.new_agent") ?>
	</a>
</div>
<br clear="all"/>
<?php } ?>

<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("page_agents.login") ?>
</th><th>
	<?php echo getlocal("page_agents.agent_name") ?>
</th><th>
	<?php echo getlocal("page_agents.agent_commonname") ?>
<?php if($page['canmodify']) { ?>
</th><th>
<?php } ?>
</th>
</tr>
</thead>
<tbody>
<?php foreach( $page['allowedAgents'] as $a ) { ?>
<tr>
	<td class="notlast">
   		<a id="ti<?php echo $a['operatorid'] ?>" href="<?php echo $webimroot ?>/operator/operator.php?op=<?php echo $a['operatorid'] ?>" class="man">
   			<?php echo htmlspecialchars(topage($a['vclogin'])) ?>
   		</a>
	</td>
	<td class="notlast">
   		<?php echo htmlspecialchars(topage($a['vclocalename'])) ?>
	</td>
	<td>
   		<?php echo htmlspecialchars(topage($a['vccommonname'])) ?>
	</td>
<?php if($page['canmodify']) { ?>
	<td>
		<a class="removelink" id="i<?php echo $a['operatorid'] ?>" href="<?php echo $webimroot ?>/operator/operators.php?act=del&amp;id=<?php echo $a['operatorid'] ?>">
			remove
		</a>
	</td>
<?php } ?>	
</tr>
<?php } ?>
</tbody>
</table>
<script type="text/javascript" language="javascript"><!--
$('a.removelink').click(function(){
	var login = $("#t"+this.id).text();
	return confirm("<?php echo getlocalforJS("page_agents.confirm", array('"+$.trim(login)+"')) ?>");
});
//--></script>

<?php 
} /* content */

require_once('inc_main.php');
?>
