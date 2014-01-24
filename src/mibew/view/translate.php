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

$page['title'] = getlocal("page.translate.title");

function tpl_content() { global $page, $mibewroot, $errors;
?>

	<?php if( $page['saved'] ) { ?>
	<?php echo getlocal("page.translate.done") ?>

	<script type="text/javascript"><!--
		if(window.opener && window.opener.location) {
			window.opener.location.reload();
		} 
		setTimeout( (function() { window.close(); }), 500 );
	//--></script>
<?php } ?>
<?php if( !$page['saved'] ) { ?>

<?php echo getlocal("page.translate.one") ?>
<br/>
<br/>
<?php
require_once('inc_errors.php');
?>

<form name="translateForm" method="post" action="<?php echo $mibewroot ?>/operator/translate.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="key" value="<?php echo safe_htmlspecialchars($page['key']) ?>"/>
<input type="hidden" name="target" value="<?php echo safe_htmlspecialchars($page['target']) ?>"/>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo safe_htmlspecialchars($page['title1']) ?></div>
			<div class="fvaluenodesc">
				<textarea name="original" disabled="disabled" cols="20" rows="5" class="wide"><?php echo form_value('original') ?></textarea>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo safe_htmlspecialchars($page['title2']) ?></div>
			<div class="fvaluenodesc">
				<textarea name="translation" cols="20" rows="5" class="wide"><?php echo form_value('translation') ?></textarea>
			</div>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src="<?php echo $mibewroot . safe_htmlspecialchars(getlocal("image.button.save")) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("button.save")) ?>"/>
		</div>
	</div>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>

<?php } ?>

<?php
} /* content */

require_once('inc_main.php');
?>