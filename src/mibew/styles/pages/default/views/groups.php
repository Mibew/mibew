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

function tpl_header() { global $page;
?>	
<script type="text/javascript" language="javascript" src="<?php echo MIBEW_WEB_ROOT ?>/js/libs/jquery.min.js"></script>
<?php
}

function tpl_content() { global $page, $errors;
?>

<?php echo getlocal("page.groups.intro") ?>
<br />
<br />
<?php 
require_once(dirname(__FILE__).'/inc_errors.php');
?>

<form name="groupsForm" method="get" action="<?php echo MIBEW_WEB_ROOT ?>/operator/groups.php">

	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="packedFormField">
		<?php echo getlocal("page.groups.sort") ?><br/>
		<select name="sortby" onchange="this.form.submit();"><?php
			foreach($page['availableOrders'] as $k) {
				echo "<option value=\"".$k['id']."\"".($k['id'] == form_value($page, "sortby") ? " selected=\"selected\"" : "").">".$k['name']."</option>";
			} ?></select>
	</div>

	<div class="packedFormField">
		<?php echo getlocal("page.groups.sortdirection") ?><br/>
		<select name="sortdirection" onchange="this.form.submit();"><?php
			foreach($page['availableDirections'] as $k) {
				echo "<option value=\"".$k['id']."\"".($k['id'] == form_value($page, "sortdirection") ? " selected=\"selected\"" : "").">".$k['name']."</option>";
			} ?></select>
	</div>

	<br clear="all"/>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>
<br />

<?php if($page['canmodify']) { ?>
<div class="tabletool">
	<img src='<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/buttons/createdep.gif' border="0" alt="" />
	<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/group.php' title="<?php echo getlocal("page.groups.new") ?>">
		<?php echo getlocal("page.groups.new") ?>
	</a>
</div>
<br clear="all"/>
<?php } ?>

<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("form.field.groupname") ?>
</th><th>
	<?php echo getlocal("form.field.groupdesc") ?>
</th><th>
	<?php echo getlocal("page_agents.status") ?>
</th><th>
	<?php echo getlocal("page.group.membersnum") ?>
</th><th>
	<?php echo getlocal("page.groups.weight") ?>
<?php if($page['canmodify']) { ?>
</th><th>
<?php } ?>
</th>
</tr>
</thead>
<tbody>
<?php
if(count($page['groups']) > 0) { 
	foreach( $page['groups'] as $grp ) { ?>
<tr>
	<td class="notlast level<?php echo $grp['level'] ?>">
   		<a href="<?php echo MIBEW_WEB_ROOT ?>/operator/group.php?gid=<?php echo $grp['groupid'] ?>" id="ti<?php echo $grp['groupid'] ?>" class="man">
   			<?php echo htmlspecialchars(topage($grp['vclocalname'])) ?>
   		</a>
	</td>
	<td class="notlast">
   		<?php echo $grp['vclocaldescription'] ? htmlspecialchars(topage($grp['vclocaldescription'])) : "&lt;none&gt;" ?>
	</td>
	<td class="notlast">
<?php if(is_online($grp)) { ?>
		<?php echo getlocal("page.groups.isonline") ?>
<?php } else if(is_away($grp)) { ?>
		<?php echo getlocal("page.groups.isaway") ?>
<?php } else { ?>
		<?php echo date_to_text(time() - ($grp['ilastseen'] ? $grp['ilastseen'] : time())) ?>
<?php } ?>
	</td>
	<td>
   		<a href="<?php echo MIBEW_WEB_ROOT ?>/operator/groupmembers.php?gid=<?php echo $grp['groupid'] ?>">
	   		<?php echo htmlspecialchars(topage($grp['inumofagents'])) ?>
   		</a>
	</td>
	<td>
		<?php echo $grp['iweight'] ?>
	</td>
<?php if($page['canmodify']) { ?>
	<td>
		<a href="<?php echo MIBEW_WEB_ROOT ?>/operator/groups.php?act=del&amp;gid=<?php echo $grp['groupid'] ?><?php print_csrf_token_in_url() ?>" id="i<?php echo $grp['groupid'] ?>" class="removelink">
			<?php echo getlocal("remove.item") ?>
		</a>
	</td>
<?php } ?>
</tr>
<?php 
	}
} else {
?>
	<tr>
	<td colspan="5">
		<?php echo getlocal("tag.pagination.no_items.elements") ?>
	</td>
	</tr>
<?php 
} 
?>
</tbody>
</table>
<script type="text/javascript" language="javascript"><!--
$('a.removelink').click(function(){
	var groupname = $("#t"+this.id).text();
	return confirm("<?php echo getlocalforJS("page.groups.confirm", array('"+$.trim(groupname)+"')) ?>");
});
//--></script>

<?php 
} /* content */

require_once(dirname(__FILE__).'/inc_main.php');
?>