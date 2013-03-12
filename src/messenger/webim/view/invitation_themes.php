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

require_once("inc_menu.php");
require_once("inc_tabbar.php");

$page['title'] = getlocal("page.preview.title");
$page['menuid'] = "settings";

function tpl_header() { global $page, $webimroot;
?>
<link href="<?php echo $webimroot ?>/styles/invitations/<?php echo $page['preview'] ?>/invite.css" rel="stylesheet" type="text/css" />
<?
} /* header */

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page.preview.intro") ?>
<br />
<br />

<form name="preview" method="get" action="<?php echo $webimroot ?>/operator/invitationthemes.php">
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("page.preview.choose") ?></div>
			<div class="fvaluenodesc">
				<select name="preview" onchange="this.form.submit();"><?php foreach($page['availablePreviews'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("preview") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
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
		<p onclick="void(0);"><?php echo getlocal("invitation.message"); ?></p>
		<div style="clear: both;"></div>
	</div>
</div>

<?php 
} /* content */

require_once('inc_main.php');
?>