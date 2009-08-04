<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
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
$page['title'] = getlocal("page_analysis.search.title");
$page['menuid'] = "history";

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page_search.intro") ?>
<br />
<br />

<form name="searchForm" method="get" action="<?php echo $webimroot ?>/operator/history.php">
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">
	
	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("page_analysis.full.text.search") ?></div>
			<div class="fvaluenodesc">
				<div id="searchtext">
					<input type="text" name="q" size="80" value="<?php echo form_value('q') ?>" class="formauth"/>
				</div>
				<div id="searchbutton">
					<input type="image" name="search" src='<?php echo $webimroot.getlocal("image.button.search") ?>' alt='<?php echo getlocal("button.search") ?>'/>
				</div>
			</div>
			<br clear="all"/>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>
<br/>


<?php if( $page['pagination'] ) { ?>

<table class="list">
<thead>
<tr class="header">
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
			<a href="<?php echo $webimroot ?>/operator/threadprocessor.php?threadid=<?php echo $chatthread['threadid'] ?>" target="_blank" onclick="this.newWindow = window.open('<?php echo $webimroot ?>/operator/threadprocessor.php?threadid=<?php echo $chatthread['threadid'] ?>', '', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=720,height=520,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><?php echo topage(htmlspecialchars($chatthread['userName'])) ?></a>
		</td>
		<td>
        	<?php echo get_user_addr(topage($chatthread['remote'])) ?>
		</td>
		<td>
        	<?php if( $chatthread['agentName'] ) {
        		echo topage(htmlspecialchars($chatthread['agentName']));
        	} else if($chatthread['groupid'] && $chatthread['groupid'] != 0 && isset($page['groupName'][$chatthread['groupid']])) {
        		echo "- ".topage(htmlspecialchars($page['groupName'][$chatthread['groupid']]))." -";
        	} 
        	?>
		</td>
		<td>
        	<?php echo topage(htmlspecialchars($chatthread['size'])) ?>
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