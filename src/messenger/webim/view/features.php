<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once("inc_menu.php");
$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

function tpl_header() { global $page, $webimroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" language="javascript">
function updateSurvey() {
	if($("#enablepresurvey").is(":checked")) {
		$(".undersurvey").show();
	} else {
		$(".undersurvey").hide();
	}
}

function updateSSL() {
	if($("#enablessl").is(":checked")) {
		$(".underssl").show();
	} else {
		$(".underssl").hide();
	}
}

$(function(){
	$("#enablepresurvey").change(function() {
		updateSurvey();
	});
	$("#enablessl").change(function() {
		updateSSL();
	});
	updateSurvey();
	updateSSL();
});
</script>
<?php
}

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("page_settings.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("features.saved") ?></div>
<?php } ?>

<form name="features" method="post" action="<?php echo $webimroot ?>/operator/features.php">
<input type="hidden" name="sent" value="true"/>
	<div>
<?php if($page['tabs']) { ?>
	<ul class="tabs">

<?php foreach($page['tabs'] as $k => $v) { if($v) { ?>
	<li><a href="<?php echo $v ?>"><?php echo $k ?></a></li>
<?php } else { ?>
	<li class="active"><a href="#"><?php echo $k ?></a></li><?php }} ?>
	</ul>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.usercanchangename') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="usercanchangename" value="on"<?php echo form_value_cb('usercanchangename') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.usercanchangename.description') ?></div>
			<br clear="all"/>
		</div>
		
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablessl') ?></div>
			<div class="fvalue">
				<input id="enablessl" type="checkbox" name="enablessl" value="on"<?php echo form_value_cb('enablessl') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablessl.description') ?></div>
			<br clear="all"/>

			<div class="subfield underssl">
				<div class="flabel"><?php echo getlocal('settings.forcessl') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="forcessl" value="on"<?php echo form_value_cb('forcessl') ? " checked=\"checked\"" : "" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.forcessl.description') ?></div>
				<br clear="all"/>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enableban') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enableban" value="on"<?php echo form_value_cb('enableban') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enableban.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablegroups') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablegroups" value="on"<?php echo form_value_cb('enablegroups') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablegroups.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablestatistics') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablestatistics" value="on"<?php echo form_value_cb('enablestatistics') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablestatistics.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablepresurvey') ?></div>
			<div class="fvalue">
				<input id="enablepresurvey" type="checkbox" name="enablepresurvey" value="on"<?php echo form_value_cb('enablepresurvey') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablepresurvey.description') ?></div>
			<br clear="all"/>

			<div class="subfield undersurvey">
				<div class="flabel"><?php echo getlocal('settings.survey.askmail') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="surveyaskmail" value="on"<?php echo form_value_cb('surveyaskmail') ? " checked=\"checked\"" : "" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askmail.description') ?></div>
				<br clear="all"/>
			</div>

			<div class="subfield undersurvey">
				<div class="flabel"><?php echo getlocal('settings.survey.askgroup') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="surveyaskgroup" value="on"<?php echo form_value_cb('surveyaskgroup') ? " checked=\"checked\"" : "" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askgroup.description') ?></div>
				<br clear="all"/>
			</div>
			
			<div class="subfield undersurvey">
				<div class="flabel"><?php echo getlocal('settings.survey.askmessage') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="surveyaskmessage" value="on"<?php echo form_value_cb('surveyaskmessage') ? " checked=\"checked\"" : "" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askmessage.description') ?></div>
				<br clear="all"/>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.popup_notification') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablepopupnotification" value="on"<?php echo form_value_cb('enablepopupnotification') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.popup_notification.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.leavemessage_captcha') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablecaptcha" value="on"<?php echo form_value_cb('enablecaptcha') ? " checked=\"checked\"" : "" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.leavemessage_captcha.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>

	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>