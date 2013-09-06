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

$page['title'] = $page['key'] ? getlocal("cannededit.title") : getlocal("cannednew.title");

function tpl_content() { global $page, $webimroot, $errors;
?>

	<?php if( $page['saved'] ) { ?>
	<?php echo getlocal("cannededit.done") ?>

	<script><!--
		if(window.opener && window.opener.location) {
			window.opener.location.reload();
		} 
		setTimeout( (function() { window.close(); }), 500 );
	//--></script>
<?php } ?>
<?php if( !$page['saved'] ) { ?>

<?php echo $page['key'] ? getlocal("cannededit.descr") : getlocal("cannednew.descr") ?>
<br/>
<br/>
<?php
require_once('inc_errors.php');
?>

<form name="cannedForm" method="post" action="<?php echo $webimroot ?>/operator/cannededit.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="key" value="<?php echo htmlspecialchars($page['key']) ?>"/>
<?php if(!$page['key']) { ?>
<input type="hidden" name="lang" value="<?php echo htmlspecialchars($page['locale']) ?>"/>
<input type="hidden" name="group" value="<?php echo htmlspecialchars($page['groupid']) ?>"/>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("cannededit.message") ?></div>
			<div class="fvaluenodesc">
				<textarea name="message" cols="20" rows="5" class="wide"><?php echo form_value('message') ?></textarea>
			</div>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src="<?php echo $webimroot . htmlspecialchars(getlocal("image.button.save")) ?>" alt="<?php echo htmlspecialchars(getlocal("button.save")) ?>"/>
		</div>
	</div>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>

<?php } ?>

<?php
} /* content */

require_once('inc_main.php');
?>
