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
require_once("inc_tabbar.php");

$page['title'] = getlocal("operator.groups.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("operator.groups.intro") ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="opgroupsForm" method="post" action="<?php echo $mibewroot ?>/operator/opgroups.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="op" value="<?php echo safe_htmlspecialchars($page['opid']) ?>"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo safe_htmlspecialchars($page['currentop']) ?>&lrm;</b>
	</p>
<?php foreach( $page['groups'] as $pm ) { ?>
	<div class="field">
		<div class="flabel"><?php echo safe_htmlspecialchars(topage($pm['vclocalname'])) ?></div>
		<div class="fvalue">
			<input type="checkbox" name="group<?php echo safe_htmlspecialchars($pm['groupid']) ?>" value="on"<?php echo form_value_mb('group',$pm['groupid']) ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
		</div>
		<div class="fdescr"> &mdash; <?php echo $pm['vclocaldescription'] ? safe_htmlspecialchars(topage($pm['vclocaldescription'])) : getlocal("operator.group.no_description") ?></div>
		<br clear="all"/>
	</div>
<?php } ?>

<?php if($page['canmodify']) { ?>
	<div class="fbutton">
		<input type="image" name="save" value="" src="<?php echo $mibewroot . safe_htmlspecialchars(getlocal("image.button.save")) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("button.save")) ?>"/>
	</div>
<?php } ?>
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>
</form>

<?php
} /* content */

require_once('inc_main.php');
?>