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

function tpl_content() { global $page, $errors;
?>

<?php echo getlocal("page.groupmembers.intro") ?>
<br />
<br />
<?php 
require_once(dirname(__FILE__).'/inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="membersForm" method="post" action="<?php echo MIBEW_WEB_ROOT ?>/operator/groupmembers.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="gid" value="<?php echo $page['groupid'] ?>"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo $page['currentgroup'] ?></b>
	</p>
<?php foreach( $page['operators'] as $pm ) { ?>
	<div class="field">
		<div class="fvaluenodesc">
			<input id="op<?php echo htmlspecialchars($pm['operatorid']); ?>" type="checkbox" name="op<?php echo htmlspecialchars($pm['operatorid']); ?>" value="on"<?php echo form_value_mb($page, 'op',$pm['operatorid']) ? " checked=\"checked\"" : "" ?>/> 
			<label for="op<?php echo htmlspecialchars($pm['operatorid']); ?>"><?php echo htmlspecialchars(topage($pm['vclocalename'])) ?> (<a href="operator.php?op=<?php echo $pm['operatorid'] ?>"><?php echo htmlspecialchars(topage($pm['vclogin'])) ?></a>)</label>
		</div>
	</div>
<?php } ?>

	<div class="fbutton">
		<input type="image" name="save" value="" src='<?php echo MIBEW_WEB_ROOT . getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
	</div>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<?php 
} /* content */

require_once(dirname(__FILE__).'/inc_main.php');
?>