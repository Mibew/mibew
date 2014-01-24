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

$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

function tpl_header() { global $page, $mibewroot;
?>
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/jquery-1.4.2.min.js"></script>
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

function tpl_content() { global $page, $mibewroot, $errors;
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

<form name="features" method="post" action="<?php echo $mibewroot ?>/operator/features.php">
<?php print_csrf_token_input() ?>
<input type="hidden" name="sent" value="true"/>
	<div>
<?php print_tabbar(); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.usercanchangename') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="usercanchangename" value="on"<?php echo form_value_cb('usercanchangename') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.usercanchangename.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablessl') ?></div>
			<div class="fvalue">
				<input id="enablessl" type="checkbox" name="enablessl" value="on"<?php echo form_value_cb('enablessl') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablessl.description') ?></div>
			<br clear="all"/>

			<div class="subfield underssl">
				<div class="flabel"><?php echo getlocal('settings.forcessl') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="forcessl" value="on"<?php echo form_value_cb('forcessl') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.forcessl.description') ?></div>
				<br clear="all"/>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablegroups') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablegroups" value="on"<?php echo form_value_cb('enablegroups') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablegroups.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablestatistics') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablestatistics" value="on"<?php echo form_value_cb('enablestatistics') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablestatistics.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablejabber') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablejabber" value="on"<?php echo form_value_cb('enablejabber') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablejabber.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enableban') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enableban" value="on"<?php echo form_value_cb('enableban') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enableban.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.enablepresurvey') ?></div>
			<div class="fvalue">
				<input id="enablepresurvey" type="checkbox" name="enablepresurvey" value="on"<?php echo form_value_cb('enablepresurvey') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.enablepresurvey.description') ?></div>
			<br clear="all"/>

			<div class="subfield undersurvey">
				<div class="flabel"><?php echo getlocal('settings.survey.askmail') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="surveyaskmail" value="on"<?php echo form_value_cb('surveyaskmail') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askmail.description') ?></div>
				<br clear="all"/>
			</div>

			<div class="subfield undersurvey">
				<div class="flabel"><?php echo getlocal('settings.survey.askgroup') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="surveyaskgroup" value="on"<?php echo form_value_cb('surveyaskgroup') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askgroup.description') ?></div>
				<br clear="all"/>
			</div>

			<div class="subfield undersurvey">
				<div class="flabel"><?php echo getlocal('settings.survey.askmessage') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="surveyaskmessage" value="on"<?php echo form_value_cb('surveyaskmessage') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askmessage.description') ?></div>
				<br clear="all"/>
			</div>

			<div class="subfield undersurvey">
				<div class="flabel"><?php echo getlocal('settings.survey.askcaptcha') ?></div>
				<div class="fvalue">
					<input type="checkbox" name="surveyaskcaptcha" value="on"<?php echo form_value_cb('surveyaskcaptcha') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<div class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askcaptcha.description') ?></div>
				<br clear="all"/>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.popup_notification') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablepopupnotification" value="on"<?php echo form_value_cb('enablepopupnotification') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.popup_notification.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.show_online_operators') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="showonlineoperators" value="on"<?php echo form_value_cb('showonlineoperators') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.show_online_operators.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('settings.leavemessage_captcha') ?></div>
			<div class="fvalue">
				<input type="checkbox" name="enablecaptcha" value="on"<?php echo form_value_cb('enablecaptcha') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('settings.leavemessage_captcha.description') ?></div>
			<br clear="all"/>
		</div>

    <?php if($page['canmodify']) { ?>
		<div class="fbutton">
			<input type="image" name="save" value="" src="<?php echo $mibewroot . safe_htmlspecialchars(getlocal("image.button.save")) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("button.save")) ?>"/>
		</div>
    <?php } ?>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>
</form>

<?php
} /* content */

require_once('inc_main.php');
?>