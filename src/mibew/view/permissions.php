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

$page['title'] = getlocal("permissions.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("permissions.intro") ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="permissionsForm" method="post" action="<?php echo $mibewroot ?>/operator/permissions.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="op" value="<?php echo safe_htmlspecialchars($page['opid']) ?>"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo safe_htmlspecialchars($page['currentop']) ?>&lrm;</b>
	</p>
<?php foreach( $page['permissionsList'] as $pm ) { ?>
	<label>
		<input type="checkbox" name="permissions<?php echo safe_htmlspecialchars($pm['id']) ?>" value="on"<?php echo form_value_mb('permissions',$pm['id']) ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/> <?php echo safe_htmlspecialchars($pm['descr']) ?>
	</label>
	<br/>
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