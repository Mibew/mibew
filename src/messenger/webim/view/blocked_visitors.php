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

require_once("inc_menu.php");
$page['title'] = getlocal("page_bans.title");
$page['menuid'] = "blocked";

function tpl_header() { global $page, $webimroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/jquery-1.4.2.min.js"></script>
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
	<?php echo getlocal("page_bans.edit") ?>
</th>
</tr>
</thead>
<tbody>
<?php 
if( $page['pagination.items'] ) {
	foreach( $page['pagination.items'] as $b ) { ?>
	<tr>
	<td class="notlast">
		<a href="<?php echo $webimroot ?>/operator/history.php?q=<?php echo $b['address']?>&type=visitor" class="man" id="ti<?php echo $b['banid'] ?>">
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
			<?php echo getlocal("remove.item") ?></a>,
		<a href="<?php echo $webimroot ?>/operator/ban.php?id=<?php echo $b['banid'] ?>">
			<?php echo getlocal("edit.item") ?></a>
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