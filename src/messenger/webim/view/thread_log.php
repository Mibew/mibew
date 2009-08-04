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

$page['title'] = getlocal("thread.chat_log");

function tpl_content() { global $page, $webimroot, $errors;
$chatthread = $page['thread'];
?>

<?php echo getlocal("thread.intro") ?>

<br/><br/>

<div class="logpane">
<div class="header">

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_name") ?>:
		</div> 
		<div class="wvalue">
			<?php echo topage(htmlspecialchars($chatthread['userName'])) ?>
		</div>
		<br clear="all"/>
		
		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_host") ?>:
		</div>
		<div class="wvalue">
			<?php echo get_user_addr(topage($chatthread['remote'])) ?>
		</div>
		<br clear="all"/>

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_browser") ?>:
		</div>
		<div class="wvalue">
			<?php echo get_useragent_version(topage($chatthread['userAgent'])) ?>
		</div>
		<br clear="all"/>

		<?php if( $chatthread['groupName'] ) { ?>
			<div class="wlabel">
				<?php echo getlocal("page.analysis.search.head_group") ?>:
			</div>
			<div class="wvalue">
				<?php echo topage(htmlspecialchars($chatthread['groupName'])) ?>
			</div>
			<br clear="all"/>
		<?php } ?>

		<?php if( $chatthread['agentName'] ) { ?>
			<div class="wlabel">
				<?php echo getlocal("page.analysis.search.head_operator") ?>:
			</div>
			<div class="wvalue">
				<?php echo topage(htmlspecialchars($chatthread['agentName'])) ?>
			</div>
			<br clear="all"/>
		<?php } ?>

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_time") ?>:
		</div>
		<div class="wvalue">
			<?php echo date_diff_to_text($chatthread['modified']-$chatthread['created']) ?> 
				(<?php echo date_to_text($chatthread['created']) ?>)
		</div>
		<br clear="all"/>
</div>

<div class="message">
<?php 
	foreach( $page['threadMessages'] as $message ) {
		echo $message;
	}
?>
</div>
</div>

<br />
<a href="<?php echo $webimroot ?>/operator/history.php">
	<?php echo getlocal("thread.back_to_search") ?></a>
<br />


<?php 
} /* content */

require_once('inc_main.php');
?>