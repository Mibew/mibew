<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

require_once(dirname(__FILE__).'/inc_menu.php');
$page['title'] = getlocal("page_agents.title");
$page['menuid'] = "operators";

function tpl_header() { global $page, $mibewroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/libs/jquery.min.js"></script>
<?php
}

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("page_agents.intro") ?>
<br />
<br />
<?php 
require_once(dirname(__FILE__).'/inc_errors.php');
?>

<form name="agentsForm" method="get" action="<?php echo $mibewroot ?>/operator/operators.php">

	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="packedFormField">
		<?php echo getlocal("page_agents.sort") ?><br/>
		<select name="sortby" onchange="this.form.submit();"><?php
			foreach($page['availableOrders'] as $k) {
				echo "<option value=\"".$k['id']."\"".($k['id'] == form_value("sortby") ? " selected=\"selected\"" : "").">".$k['name']."</option>";
			} ?></select>
	</div>

	<div class="packedFormField">
		<?php echo getlocal("page_agents.sortdirection") ?><br/>
		<select name="sortdirection" onchange="this.form.submit();"><?php
			foreach($page['availableDirections'] as $k) {
				echo "<option value=\"".$k['id']."\"".($k['id'] == form_value("sortdirection") ? " selected=\"selected\"" : "").">".$k['name']."</option>";
			} ?></select>
	</div>

	<br clear="all"/>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>
<br />

<?php if($page['canmodify']) { ?>
<div class="tabletool">
	<img src='<?php echo $mibewroot ?>/styles/pages/default/images/buttons/createagent.gif' border="0" alt="" />
	<a href='<?php echo $mibewroot ?>/operator/operator.php' title="<?php echo getlocal("page_agents.new_agent") ?>">
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
	<?php echo getlocal("page_agents.status") ?>
<?php if($page['canmodify']) { ?>
</th><th>
</th><th>
<?php } ?>
</th>
</tr>
</thead>
<tbody>
<?php foreach( $page['allowedAgents'] as $a ) { ?>
<tr>
	<td class="notlast">
   		<a id="ti<?php echo $a['operatorid'] ?>" href="<?php echo $mibewroot ?>/operator/operator.php?op=<?php echo $a['operatorid'] ?>" class="man">
   			<?php echo htmlspecialchars(topage($a['vclogin'])) ?>
   		</a>
	</td>
	<td class="notlast">
   		<?php echo htmlspecialchars(topage($a['vclocalename'])) ?> / <?php echo htmlspecialchars(topage($a['vccommonname'])) ?>
	</td>
	<td class="notlast">
<?php if(operator_is_available($a)) { ?>
		<?php echo getlocal("page_agents.isonline") ?>
<?php } else if(operator_is_away($a)) { ?>
		<?php echo getlocal("page_agents.isaway") ?>
<?php } else { ?>
		<?php echo date_to_text(time() - $a['time']) ?>
<?php } ?>
	</td>
<?php if($page['canmodify']) { ?>
	<td>
<?php if(operator_is_disabled($a)){ ?>
		<a href="<?php echo $mibewroot ?>/operator/operators.php?act=enable&amp;id=<?php echo $a['operatorid'] ?>"><?php echo getlocal("page_agents.enable.agent") ?></a>
<?php }else{ ?>
		<a href="<?php echo $mibewroot ?>/operator/operators.php?act=disable&amp;id=<?php echo $a['operatorid'] ?>"><?php echo getlocal("page_agents.disable.agent") ?></a>
<?php } ?>
	</td>
	<td>
		<a class="removelink" id="i<?php echo $a['operatorid'] ?>" href="<?php echo $mibewroot ?>/operator/operators.php?act=del&amp;id=<?php echo $a['operatorid'] ?><?php print_csrf_token_in_url() ?>">
			<?php echo getlocal("remove.item") ?>
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

require_once(dirname(__FILE__).'/inc_main.php');
?>