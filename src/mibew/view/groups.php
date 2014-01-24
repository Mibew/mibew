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
$page['title'] = getlocal("page.groups.title");
$page['menuid'] = "groups";

function tpl_header() { global $page, $mibewroot;
?>
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/jquery-1.4.2.min.js"></script>
<?php
}

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("page.groups.intro") ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>

<?php if($page['canmodify']) { ?>
<div class="tabletool">
	<img src="<?php echo $mibewroot ?>/images/buttons/createdep.gif" border="0" alt="" />
	<a href="<?php echo $mibewroot ?>/operator/group.php" title="<?php echo getlocal("page.groups.new") ?>">
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
	<td class="notlast">
		<a href="<?php echo $mibewroot ?>/operator/group.php?gid=<?php echo urlencode($grp['groupid']) ?>" id="ti<?php echo safe_htmlspecialchars($grp['groupid']) ?>" class="man">
			<?php echo safe_htmlspecialchars(topage($grp['vclocalname'])) ?>
		</a>
	</td>
	<td class="notlast">
		<?php echo $grp['vclocaldescription'] ? safe_htmlspecialchars(topage($grp['vclocaldescription'])) : "&lt;" . getlocal("page.groups.none") . "&gt;" ?>
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
		<a href="<?php echo $mibewroot ?>/operator/groupmembers.php?gid=<?php echo urlencode($grp['groupid']) ?>">
			<?php echo safe_htmlspecialchars(topage($grp['inumofagents'])) ?>
		</a>
	</td>
<?php if($page['canmodify']) { ?>
	<td>
		<a href="<?php echo $mibewroot ?>/operator/groups.php?act=del&amp;gid=<?php echo urlencode($grp['groupid']) ?><?php print_csrf_token_in_url() ?>" id="i<?php echo safe_htmlspecialchars($grp['groupid']) ?>" class="removelink">
			<?php echo getlocal("page.groups.remove") ?>
		</a>
	</td>
<?php } ?>
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
<script type="text/javascript" language="javascript"><!--
$('a.removelink').click(function(){
	var groupname = $("#t"+this.id).text();
	return confirm("<?php echo getlocalforJS("page.groups.confirm", array('"+$.trim(groupname)+"')) ?>");
});
//--></script>

<?php 
} /* content */

require_once('inc_main.php');
?>