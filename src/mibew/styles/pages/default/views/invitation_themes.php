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

function tpl_header() { global $page;
?>
<link href="<?php echo MIBEW_WEB_ROOT ?>/styles/invitations/<?php echo $page['preview'] ?>/invite.css" rel="stylesheet" type="text/css" />
<?
} /* header */

function tpl_content() { global $page;
?>

<?php echo getlocal("page.preview.intro") ?>
<br />
<br />

<form name="preview" method="get" action="<?php echo MIBEW_WEB_ROOT ?>/operator/invitationthemes.php">
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<label for="preview" class="flabel"><?php echo getlocal("page.preview.choose") ?></label>
			<div class="fvaluenodesc">
				<select id="preview" name="preview" onchange="this.form.submit();"><?php foreach($page['availablePreviews'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("preview") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			</div>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<div id="mibewinvitation">
	<div id="mibewinvitationpopup">
		<div id="mibewinvitationclose">
			<a onclick="void(0);" href="javascript:void(0);">&times;</a>
		</div>
		<h1 onclick="void(0);"><?php echo $page['operatorName'] ?></h1>
		<div id="mibewinvitationframe"><?php echo getlocal("invitation.message"); ?></div>
		<div id="mibewinvitationaccept" onclick="void(0);"><?php echo getlocal("invitation.accept.caption"); ?></div>
		<div style="clear: both;"></div>
	</div>
</div>

<?php 
} /* content */

require_once(dirname(__FILE__).'/inc_main.php');
?>