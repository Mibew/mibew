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

function tpl_header($page) {
	if(isset($page['localeLinks'])) {
?>	
<script type="text/javascript" language="javascript" src="<?php echo MIBEW_WEB_ROOT ?>/js/libs/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo MIBEW_WEB_ROOT ?>/js/compiled/locale.js"></script>
<?php
	}
}

function menuseparator() {
	static $menu_items_count = 0;
	$menu_items_count++;
	if(($menu_items_count%3) == 0) { echo "</tr><tr>"; }
}

function tpl_content($page) {
?>

<br/>

<?php if( $page['needChangePassword'] ) { ?>
<div id="formmessage"><?php echo getlocal("error.no_password") ?> <?php echo getlocal2("error.no_password.visit_profile", array($page['profilePage'])) ?></div>
<br/>
<?php } else if( $page['needUpdate'] ) { ?>
<div id="formmessage"><?php echo getlocal2("install.updatedb",array($page['updateWizard'])) ?></div>
<br/>
<?php } else if($page['newFeatures']) { ?>
<div><div id="formmessage"><?php echo getlocal2("install.newfeatures",array($page['featuresPage'], MIBEW_VERSION)) ?></div></div>
<br/>
<?php } ?>

<table id="dashboard">
<tr>
	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/visitors.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/users.php'>
			<?php echo getlocal('topMenu.users') ?></a>
		<?php echo getlocal('page_client.pending_users') ?>
	</td>
	<?php menuseparator(); ?>

	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/history.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/history.php'>
			<?php echo getlocal('page_analysis.search.title') ?></a>
		<?php echo getlocal('content.history') ?>
	</td>
	<?php menuseparator(); ?>

<?php if($page['showstat']) { ?>
	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/stat.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/statistics.php'>
			<?php echo getlocal('statistics.title') ?></a>
		<?php echo getlocal('statistics.description') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>

<?php if( $page['showban'] ) { ?>	
	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/blocked.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/blocked.php'>
			<?php echo getlocal('menu.blocked') ?></a>
		<?php echo getlocal('content.blocked') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>

	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/canned.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/canned.php'>
			<?php echo getlocal('menu.canned') ?></a>
		<?php echo getlocal('canned.descr') ?>
	</td>
	<?php menuseparator(); ?>

<?php if( $page['showadmin'] ) { ?>
	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/getcode.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/getcode.php'>
			<?php echo getlocal('leftMenu.client_gen_button') ?></a>
		<?php echo getlocal('admin.content.client_gen_button') ?>
	</td>
	<?php menuseparator(); ?>
	
	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/operators.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/operators.php'>
			<?php echo getlocal('leftMenu.client_agents') ?></a>
		<?php echo getlocal('admin.content.client_agents') ?>
	</td>
	<?php menuseparator(); ?>

	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/dep.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/groups.php'>
			<?php echo getlocal('menu.groups') ?></a>
		<?php echo getlocal('menu.groups.content') ?>
	</td>
	<?php menuseparator(); ?>

	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/settings.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/settings.php'>
			<?php echo getlocal('leftMenu.client_settings') ?></a>
		<?php echo getlocal('admin.content.client_settings') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>

<?php if(isset($page['currentopid']) && $page['currentopid']) {?>
	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/profile.gif"  alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/operator.php?op=<?php echo $page['currentopid'] ?>'>
			<?php echo getlocal('menu.profile') ?></a>
		<?php echo getlocal('menu.profile.content') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>

<?php if(isset($page) && isset($page['localeLinks'])) { ?>
	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/locale.gif"  alt=""/>
		<a href='#' id="changelang">
			<?php echo getlocal('menu.locale') ?></a>
		<?php echo getlocal('menu.locale.content') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>

<?php if( $page['showadmin'] ) { ?>
	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/updates.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/updates.php'>
			<?php echo getlocal('menu.updates') ?></a>
		<?php echo getlocal('menu.updates.content') ?>
	</td>
	<?php menuseparator(); ?>
<?php } ?>

	<td class="dashitem">
		<img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/exit.gif" alt=""/>
		<a href='<?php echo MIBEW_WEB_ROOT ?>/operator/logout.php'>
			<?php echo getlocal('topMenu.logoff') ?></a>
		<?php echo getlocal('content.logoff') ?>
	</td>
</tr>

</table>

<?php if(isset($page) && isset($page['localeLinks'])) { ?>
<div id="dashlocalesPopup">
	<a href="#" id="dashlocalesPopupClose"><img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/close.gif" alt="X"/></a>
	<h2><img src="<?php echo MIBEW_WEB_ROOT ?>/styles/pages/default/images/dash/locale.gif"  alt=""/>
	<b><?php echo getlocal("lang.choose") ?></b></h2>
	<ul class="locales">
<?php foreach($page['localeLinks'] as $id => $title) { ?>
		<li<?php echo CURRENT_LOCALE == $id ? " class=\"active\"" : "" ?> ><a href='?locale=<?php echo $id ?>'><?php echo $title ?></a></li>
<?php } ?>
	</ul>
</div>
<div id="backgroundPopup"></div> 
<?php } ?>

<?php 
} /* content */

require_once(dirname(__FILE__).'/inc_main.php');
?>