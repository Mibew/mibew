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

function tpl_content() { global $page, $webimroot;
?>

	<?php echo getlocal("admin.content.description") ?>
<br/>
<br/>
<?php if( $page['needUpdate'] ) { ?>
<div id="formmessage"><?php echo getlocal2("install.updatedb",array($page['updateWizard'])) ?></div>
<br/>
<?php } ?>

<div id="dashboard">

	<div class="dashitem">
		<a href='<?php echo $webimroot ?>/operator/users.php'>
			<?php echo getlocal('topMenu.users') ?></a>
		<?php echo getlocal('page_client.pending_users') ?>
	</div>	

	<div class="dashitem">
		<a href='<?php echo $webimroot ?>/operator/history.php'>
			<?php echo getlocal('page_analysis.search.title') ?></a>
		<?php echo getlocal('content.history') ?>
	</div>
<?php if( $page['showban'] ) { ?>
	
	<div class="dashitem">
		<a href='<?php echo $webimroot ?>/operator/blocked.php'>
			<?php echo getlocal('menu.blocked') ?></a>
		<?php echo getlocal('content.blocked') ?>
	</div>
<?php } ?>

<?php if( $page['showadmin'] ) { ?>
	<br clear="all"/>
	
	<div class="dashitem">
		<a href='<?php echo $webimroot ?>/operator/operators.php'>
			<?php echo getlocal('leftMenu.client_agents') ?></a>
		<?php echo getlocal('admin.content.client_agents') ?>
	</div>
	
	<div class="dashitem">
		<a href='<?php echo $webimroot ?>/operator/getcode.php'>
			<?php echo getlocal('leftMenu.client_gen_button') ?></a>
		<?php echo getlocal('admin.content.client_gen_button') ?>
	</div>

	<div class="dashitem">
		<a href='<?php echo $webimroot ?>/operator/settings.php'>
			<?php echo getlocal('leftMenu.client_settings') ?></a>
		<?php echo getlocal('admin.content.client_settings') ?>
	</div>
<?php } ?>

	<br clear="all"/>

	<div class="dashitem">
		<a href='<?php echo $webimroot ?>/operator/logout.php'>
			<?php echo getlocal('topMenu.logoff') ?></a>
		<?php echo getlocal('content.logoff') ?>
	</div>

</div>
	
<?php 
} /* content */

require_once('inc_main.php');
?>