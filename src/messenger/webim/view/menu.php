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
$page['title'] = getlocal("topMenu.admin");
$page['menuid'] = "main";

function tpl_header() { global $page, $webimroot;
	if(isset($page) && isset($page['localeLinks'])) {
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/locale.js"></script>
<?php
	}
}

function menuseparator() {
	global $menuItemsCount;
	$menuItemsCount++;
	if(($menuItemsCount%3) == 0) { echo "</tr><tr>"; }
}

function tpl_content() { global $page, $webimroot, $current_locale, $menuItemsCount;
?>

<br/>   
<?php if( $page['needUpdate'] ) { ?>
<div id="formmessage"><?php echo getlocal2("install.updatedb",array($page['updateWizard'])) ?></div>
<br/>
<?php } ?>

<table id="dashboard">
<tr>
	<td class="dashitem">
		<img src="/webim/images/dash/visitors.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/users.php'>
			<?php echo getlocal('topMenu.users') ?></a>
		<?php echo getlocal('page_client.pending_users') ?>
	</td>	

	<td class="dashitem">
		<img src="/webim/images/dash/history.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/history.php'>
			<?php echo getlocal('page_analysis.search.title') ?></a>
		<?php echo getlocal('content.history') ?>
	</td>
<?php 
$menuItemsCount = 2;
if( $page['showban'] ) {
?>

<?php if($page['showstat']) { ?>
	<td class="dashitem">
		<img src="/webim/images/dash/stat.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/statistics.php'>
			<?php echo getlocal('statistics.title') ?></a>
		<?php echo getlocal('statistics.description') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>
	
	<td class="dashitem">
		<img src="/webim/images/dash/blocked.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/blocked.php'>
			<?php echo getlocal('menu.blocked') ?></a>
		<?php echo getlocal('content.blocked') ?>
	</td>
	<?php menuseparator(); ?>
<?php 
}
if( $page['showadmin'] ) { ?>
	
	<td class="dashitem">
		<img src="/webim/images/dash/operators.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/operators.php'>
			<?php echo getlocal('leftMenu.client_agents') ?></a>
		<?php echo getlocal('admin.content.client_agents') ?>
	</td>
	<?php menuseparator(); ?>

<?php if($page['showdep']) { ?>
	<td class="dashitem">
		<img src="/webim/images/dash/dep.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/departments.php'>
			<?php echo getlocal('menu.departments') ?></a>
		<?php echo getlocal('menu.departments.content') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>	
	<td class="dashitem">
		<img src="/webim/images/dash/getcode.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/getcode.php'>
			<?php echo getlocal('leftMenu.client_gen_button') ?></a>
		<?php echo getlocal('admin.content.client_gen_button') ?>
	</td>
	<?php menuseparator(); ?>

	<td class="dashitem">
		<img src="/webim/images/dash/settings.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/settings.php'>
			<?php echo getlocal('leftMenu.client_settings') ?></a>
		<?php echo getlocal('admin.content.client_settings') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>

<?php if(isset($page) && isset($page['localeLinks'])) { ?>
	<td class="dashitem">
		<img src="/webim/images/dash/locale.gif"  alt=""/>
		<a href='#' id="changelang">
			<?php echo getlocal('menu.locale') ?></a>
		<?php echo getlocal('menu.locale.content') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>

	<td class="dashitem">
		<img src="/webim/images/dash/exit.gif" alt=""/>
		<a href='<?php echo $webimroot ?>/operator/logout.php'>
			<?php echo getlocal('topMenu.logoff') ?></a>
		<?php echo getlocal('content.logoff') ?>
	</td>
</tr>

</table>

<?php if(isset($page) && isset($page['localeLinks'])) { ?>
<div id="dashlocalesPopup">
	<a href="#" id="dashlocalesPopupClose"><img src="/webim/images/dash/close.gif" alt="X"/></a>
	<h2><img src="/webim/images/dash/locale.gif"  alt=""/>
	<b><?php echo getlocal("lang.choose") ?></b></h2>
	<ul class="locales">
<?php foreach($page['localeLinks'] as $id => $title) { ?>
		<li<?php echo $current_locale == $id ? " class=\"active\"" : "" ?> ><a href='?locale=<?php echo $id ?>'><?php echo $title ?></a></li>
<?php } ?>
	</ul>
</div>
<div id="backgroundPopup"></div> 
<?php } ?>

<?php 
} /* content */

require_once('inc_main.php');
?>