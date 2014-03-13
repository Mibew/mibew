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
$page['title'] = getlocal("purge.title");
$page['menuid'] = "purge";

function tpl_content() { global $page, $mibewroot;
?>

<br />
<br />

<form name="searchForm" method="get" action="<?php echo $mibewroot ?>/operator/purge.php">
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">
	
	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("purge.search") ?></div>
			<div class="fvaluenodesc">
				<div>
					<input type="text" name="start" size="20" value="<?php echo(strlen(form_value('start')) ? form_value('start') : '1 Jan 1970') ?>" /> &#8230;
					<input type="text" name="end" size="20" value="<?php echo(strlen(form_value('end')) ? form_value('end') : 'Yesterday') ?>"/>
				</div>
				<div id="searchbutton">
					<input type="image" name="search" src="<?php echo $mibewroot . safe_htmlspecialchars(getlocal("image.button.search")) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("button.search")) ?>"/>
				</div>
			</div>
			<br clear="all"/>
		</div>
	</div>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>
<br/>


<?php if( $page['pagination'] ) { ?>

<form name="searchForm" method="post" action="<?php echo $mibewroot ?>/operator/purge.php">

<table class="list">
<thead>
<tr class="header">
<th>
	<script type="text/javascript">
		function toggle_all (truth) {
			var inputs = document.getElementsByTagName('input');
			for (var i = 0; i < inputs.length; i++) {
				var input = inputs[i];
				if (input.name == 'thread[]') {
					input.checked = truth;
				}
			}
		}
		document.write('<input type="checkbox" onclick="toggle_all(this.checked)">');
	</script>
</th>
<th>
	<?php echo getlocal("page.analysis.search.head_name") ?>
</th><th>
	<?php echo getlocal("page.analysis.search.head_host") ?>
</th><th>
	<?php echo getlocal("page.analysis.search.head_operator") ?>
</th><th>
	<?php echo getlocal("page.analysis.search.head_messages") ?>
</th><th>
	<?php echo getlocal("page.analysis.search.head_time") ?>
</th></tr>
</thead>
<tbody>
<?php
if( $page['pagination.items'] ) {
	foreach( $page['pagination.items'] as $chatthread ) { ?>
	<tr>
		<td>
			<input type="checkbox" name="thread[]" value="<?php echo $chatthread['threadid'] ?>">
		</td>
		<td>
			<a href="<?php echo $mibewroot ?>/operator/threadprocessor.php?threadid=<?php echo urlencode($chatthread['threadid']) ?>" target="_blank" onclick="this.newWindow = window.open('<?php echo $mibewroot ?>/operator/threadprocessor.php?threadid=<?php echo urlencode($chatthread['threadid']) ?>', '', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=720,height=520,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><?php echo topage(safe_htmlspecialchars($chatthread['userName'])) ?></a>
		</td>
		<td>
		<?php echo get_user_addr(topage($chatthread['remote'])) ?>
		</td>
		<td>
		<?php if( $chatthread['agentName'] ) {
			echo topage(safe_htmlspecialchars($chatthread['agentName']));
		} else if($chatthread['groupid'] && $chatthread['groupid'] != 0 && isset($page['groupName'][$chatthread['groupid']])) {
			echo "- ".topage(safe_htmlspecialchars($page['groupName'][$chatthread['groupid']]))." -";
		}
		?>
		</td>
		<td>
		<?php echo topage(safe_htmlspecialchars($chatthread['size'])) ?>
		</td>
		<td>
			<?php echo date_diff_to_text($chatthread['modified']-$chatthread['created']) ?>, <?php echo date_to_text($chatthread['created']) ?>
		</td>
	</tr>
<?php
	}
} else {
?>
	<tr>
	<td colspan="5">
		<?php echo getlocal("tag.pagination.no_items") ?>
	</td>
	</tr>
<?php
}
?>
</tbody>
</table>
<p><input type="submit" value="<?php echo safe_htmlspecialchars(getlocal("button.purge")) ?>"/></p>
</form>

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