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
require_once(dirname(__FILE__).'/inc_tabbar.php');

function tpl_content() { global $page;
?>

<?php echo getlocal("operator.groups.intro") ?>
<br />
<br />
<?php 
require_once(dirname(__FILE__).'/inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="opgroupsForm" method="post" action="<?php echo MIBEW_WEB_ROOT ?>/operator/opgroups.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="op" value="<?php echo $page['opid'] ?>"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo $page['currentop'] ?>&lrm;</b>
	</p>
<?php foreach( $page['groups'] as $pm ) { ?>
	<div class="field level<?php echo $pm['level'] ?>">
		<label for="group<?php echo htmlspecialchars($pm['groupid']); ?>" class="flabel"><?php echo htmlspecialchars(topage($pm['vclocalname'])) ?></label>
		<div class="fvalue">
			<input id="group<?php echo htmlspecialchars($pm['groupid']); ?>" type="checkbox" name="group<?php echo htmlspecialchars($pm['groupid']); ?>" value="on"<?php echo form_value_mb($page, 'group',$pm['groupid']) ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
		</div>
		<label for="group<?php echo htmlspecialchars($pm['groupid']); ?>" class="fdescr"> &mdash; <?php echo $pm['vclocaldescription'] ? htmlspecialchars(topage($pm['vclocaldescription'])) : getlocal("operator.group.no_description") ?></label>
		<br clear="all"/>
	</div>
<?php } ?>

<?php if($page['canmodify']) { ?>
	<div class="fbutton">
		<input type="image" name="save" value="" src='<?php echo MIBEW_WEB_ROOT . getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
	</div>
<?php } ?>
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<?php 
} /* content */

require_once(dirname(__FILE__).'/inc_main.php');
?>