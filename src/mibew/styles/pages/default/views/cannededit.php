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

function tpl_content($page) {
?>

	<?php if( $page['saved'] ) { ?>
	<?php echo getlocal("cannededit.done") ?>

	<script type="text/javascript"><!--
		if(window.opener && window.opener.location) {
			window.opener.location.reload();
		} 
		setTimeout( (function() { window.close(); }), 1500 );
	//--></script>
<?php } ?>
<?php if( !$page['saved'] ) { ?>

<?php echo $page['key'] ? getlocal("cannededit.descr") : getlocal("cannednew.descr") ?>
<br/>
<br/>
<?php 
require_once(dirname(__FILE__).'/inc_errors.php');
?>

<form name="cannedForm" method="post" action="<?php echo MIBEW_WEB_ROOT ?>/operator/cannededit.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="key" value="<?php echo $page['key'] ?>"/>
<?php if(!$page['key']) { ?>
<input type="hidden" name="lang" value="<?php echo $page['locale'] ?>"/>
<input type="hidden" name="group" value="<?php echo $page['groupid'] ?>"/>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<label for="titlefield" class="flabel"><?php echo getlocal("canned.message_title") ?></label>
			<div class="fvaluenodesc">
				<input id="titlefield" type="text" name="title" class="wide" maxlength="100" value="<?php echo form_value($page, 'title') ?>"/>
			</div>
		</div>

		<div class="field">
			<label for="message" class="flabel"><?php echo getlocal("cannededit.message") ?></label>
			<div class="fvaluenodesc">
				<textarea id="message" name="message" cols="20" rows="5" class="wide"><?php echo form_value($page, 'message') ?></textarea>
			</div>
		</div>
	
		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo MIBEW_WEB_ROOT . getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>

<?php } ?>

<?php 
} /* content */

require_once(dirname(__FILE__).'/inc_main.php');
?>