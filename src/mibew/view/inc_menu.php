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

function menuli($name) {
	global $page;
	if(isset($page) && isset($page['menuid']) && $name == $page['menuid']) {
		echo " class=\"active\"";
	}
	return "";
}

function tpl_menu() { global $page, $mibewroot, $errors;
	if(isset($page['isOnline']) && !$page['isOnline']) { ?>
			<li id="offwarn">
				<img src="<?php echo $mibewroot ?>/images/dash/warn.gif" alt="" width="24" height="24"/>
				<p><?php echo getlocal2("menu.goonline",array($mibewroot."/operator/users.php?nomenu")) ?></p>
			</li>		
<?php }
	if(isset($page['operator'])) { ?>
			<li>
				<h2><?php echo getlocal('right.main') ?></h2>
				<ul class="submenu">
					<li<?php menuli("main")?>><a href="<?php echo $mibewroot ?>/operator/index.php"><?php echo getlocal('topMenu.main') ?></a></li>
					<li<?php menuli("users")?>><a href="<?php echo $mibewroot ?>/operator/users.php"><?php echo getlocal('topMenu.users') ?></a> <span class="small">(<a class="inner" href="<?php echo $mibewroot ?>/operator/users.php?nomenu"><?php echo getlocal('topMenu.users.nomenu') ?></a>)</span></li>
					<li<?php menuli("history")?>><a href="<?php echo $mibewroot ?>/operator/history.php"><?php echo getlocal('page_analysis.search.title') ?></a></li>
<?php if(isset($page['showstat']) && $page['showstat']) { ?>
					<li<?php menuli("statistics")?>><a href="<?php echo $mibewroot ?>/operator/statistics.php"><?php echo getlocal('statistics.title') ?></a></li>
<?php } ?>
<?php if(isset($page['showban']) && $page['showban']) { ?>
					<li<?php menuli("blocked")?>><a href="<?php echo $mibewroot ?>/operator/blocked.php"><?php echo getlocal('menu.blocked') ?></a></li>
<?php } ?>
				</ul>
			</li>
			<li>
				<h2><?php echo getlocal('right.administration') ?></h2>
				<ul class="submenu">
					<li<?php menuli("canned")?>><a href="<?php echo $mibewroot ?>/operator/canned.php"><?php echo getlocal('menu.canned') ?></a></li>
<?php if(isset($page['showadmin']) && $page['showadmin']) { ?>
					<li<?php menuli("getcode")?>><a href="<?php echo $mibewroot ?>/operator/getcode.php"><?php echo getlocal('leftMenu.client_gen_button') ?></a></li>
					<li<?php menuli("operators")?>><a href="<?php echo $mibewroot ?>/operator/operators.php"><?php echo getlocal('leftMenu.client_agents') ?></a></li>
<?php if(isset($page['showgroups']) && $page['showgroups']) { ?>
					<li<?php menuli("groups")?>><a href="<?php echo $mibewroot ?>/operator/groups.php"><?php echo getlocal('menu.groups') ?></a></li>
<?php } ?>
					<li<?php menuli("settings")?>><a href="<?php echo $mibewroot ?>/operator/settings.php"><?php echo getlocal('leftMenu.client_settings') ?></a></li>
					<li<?php menuli("translate")?>><a href="<?php echo $mibewroot ?>/operator/translate.php"><?php echo getlocal('menu.translate') ?></a></li>
					<li<?php menuli("updates")?>><a href="<?php echo $mibewroot ?>/operator/updates.php"><?php echo getlocal('menu.updates') ?></a></li>
<?php } ?>
<?php if(isset($page['showadmin']) && $page['showadmin'] || isset($page['shownotifications']) && $page['shownotifications']) { ?>
					<li<?php menuli("notifications")?>><a href="<?php echo $mibewroot ?>/operator/notifications.php"><?php echo getlocal('menu.notifications') ?></a></li>
<?php } ?>
<?php if(isset($page['currentopid']) && $page['currentopid']) {?>
					<li<?php menuli("profile")?>><a href="<?php echo $mibewroot ?>/operator/operator.php?op=<?php echo urlencode($page['currentopid']) ?>"><?php echo getlocal('menu.profile') ?></a></li>
<?php } ?>
				</ul>
			</li>
			<li>
				<h2><?php echo getlocal('right.other') ?></h2>
				<ul class="submenu">
					<li><a href="<?php echo $mibewroot ?>/operator/logout.php"><?php echo getlocal('topMenu.logoff') ?></a></li>
				</ul>
			</li>
<?php
	}
}
?>