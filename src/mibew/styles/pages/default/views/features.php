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

function tpl_header() { global $page, $mibewroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/libs/jquery.min.js"></script>
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

function updateGroups(){
	if($("#enablegroups").is(":checked")) {
		$(".undergroups").show();
	} else {
		$(".undergroups").hide();
	}
}

$(function(){
	$("#enablepresurvey").change(function() {
		updateSurvey();
	});
	$("#enablessl").change(function() {
		updateSSL();
	});
	$("#enablegroups").change(function() {
		updateGroups();
	});
	updateSurvey();
	updateSSL();
	updateGroups();
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
require_once(dirname(__FILE__).'/inc_errors.php');
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
			<label for="usercanchangename" class="flabel"><?php echo getlocal('settings.usercanchangename') ?></label>
			<div class="fvalue">
				<input id="usercanchangename" type="checkbox" name="usercanchangename" value="on"<?php echo form_value_cb('usercanchangename') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="usercanchangename" class="fdescr"> &mdash; <?php echo getlocal('settings.usercanchangename.description') ?></label>
			<br clear="all"/>
		</div>
		
		<div class="field">
			<label for="enablessl" class="flabel"><?php echo getlocal('settings.enablessl') ?></label>
			<div class="fvalue">
				<input id="enablessl" type="checkbox" name="enablessl" value="on"<?php echo form_value_cb('enablessl') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="enablessl" class="fdescr"> &mdash; <?php echo getlocal('settings.enablessl.description') ?></label>
			<br clear="all"/>

			<div class="subfield underssl">
				<label for="forcessl" class="flabel"><?php echo getlocal('settings.forcessl') ?></label>
				<div class="fvalue">
					<input id="forcessl" type="checkbox" name="forcessl" value="on"<?php echo form_value_cb('forcessl') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<label for="forcessl" class="fdescr"> &mdash; <?php echo getlocal('settings.forcessl.description') ?></label>
				<br clear="all"/>
			</div>
		</div>

		<div class="field">
			<label for="enablegroups" class="flabel"><?php echo getlocal('settings.enablegroups') ?></label>
			<div class="fvalue">
				<input id="enablegroups" type="checkbox" name="enablegroups" value="on"<?php echo form_value_cb('enablegroups') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="enablegroups" class="fdescr"> &mdash; <?php echo getlocal('settings.enablegroups.description') ?></label>
			<br clear="all"/>

			<div class="subfield undergroups">
				<label for="enablegroupsisolation" class="flabel"><?php echo getlocal('settings.enablegroupsisolation') ?></label>
				<div class="fvalue">
					<input id="enablegroupsisolation" type="checkbox" name="enablegroupsisolation" value="on"<?php echo form_value_cb('enablegroupsisolation') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<label for="enablegroupsisolation" class="fdescr"> &mdash; <?php echo getlocal('settings.enablegroupsisolation.description') ?></label>
				<br clear="all"/>
			</div>
		</div>

		<div class="field">
			<label for="enablestatistics" class="flabel"><?php echo getlocal('settings.enablestatistics') ?></label>
			<div class="fvalue">
				<input id="enablestatistics" type="checkbox" name="enablestatistics" value="on"<?php echo form_value_cb('enablestatistics') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="enablestatistics" class="fdescr"> &mdash; <?php echo getlocal('settings.enablestatistics.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
   			<label for="enabletracking" class="flabel"><?php echo getlocal('settings.enabletracking') ?></label>
   			<div class="fvalue">
   				<input id="enabletracking" type="checkbox" name="enabletracking" value="on"<?php echo form_value_cb('enabletracking') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
   			</div>
   			<label for="enabletracking" class="fdescr"> &mdash; <?php echo getlocal('settings.enabletracking.description') ?></label>
   			<br clear="all"/>
   		</div>

		<div class="field">
			<label for="enableban" class="flabel"><?php echo getlocal('settings.enableban') ?></label>
			<div class="fvalue">
				<input id="enableban" type="checkbox" name="enableban" value="on"<?php echo form_value_cb('enableban') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="enableban" class="fdescr"> &mdash; <?php echo getlocal('settings.enableban.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="enablepresurvey" class="flabel"><?php echo getlocal('settings.enablepresurvey') ?></label>
			<div class="fvalue">
				<input id="enablepresurvey" type="checkbox" name="enablepresurvey" value="on"<?php echo form_value_cb('enablepresurvey') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="enablepresurvey" class="fdescr"> &mdash; <?php echo getlocal('settings.enablepresurvey.description') ?></label>
			<br clear="all"/>

			<div class="subfield undersurvey">
				<label for="surveyaskmail" class="flabel"><?php echo getlocal('settings.survey.askmail') ?></label>
				<div class="fvalue">
					<input id="surveyaskmail" type="checkbox" name="surveyaskmail" value="on"<?php echo form_value_cb('surveyaskmail') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<label for="surveyaskmail" class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askmail.description') ?></label>
				<br clear="all"/>
			</div>

			<div class="subfield undersurvey">
				<label for="surveyaskgroup" class="flabel"><?php echo getlocal('settings.survey.askgroup') ?></label>
				<div class="fvalue">
					<input id="surveyaskgroup" type="checkbox" name="surveyaskgroup" value="on"<?php echo form_value_cb('surveyaskgroup') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<label for="surveyaskgroup" class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askgroup.description') ?></label>
				<br clear="all"/>
			</div>
			
			<div class="subfield undersurvey">
				<label for="surveyaskmessage" class="flabel"><?php echo getlocal('settings.survey.askmessage') ?></label>
				<div class="fvalue">
					<input id="surveyaskmessage" type="checkbox" name="surveyaskmessage" value="on"<?php echo form_value_cb('surveyaskmessage') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
				</div>
				<label for="surveyaskmessage" class="fdescr"> &mdash; <?php echo getlocal('settings.survey.askmessage.description') ?></label>
				<br clear="all"/>
			</div>
		</div>

		<div class="field">
			<label for="enablepopupnotification" class="flabel"><?php echo getlocal('settings.popup_notification') ?></label>
			<div class="fvalue">
				<input id="enablepopupnotification" type="checkbox" name="enablepopupnotification" value="on"<?php echo form_value_cb('enablepopupnotification') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="enablepopupnotification" class="fdescr"> &mdash; <?php echo getlocal('settings.popup_notification.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="showonlineoperators" class="flabel"><?php echo getlocal('settings.show_online_operators') ?></label>
			<div class="fvalue">
				<input id="showonlineoperators" type="checkbox" name="showonlineoperators" value="on"<?php echo form_value_cb('showonlineoperators') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="showonlineoperators" class="fdescr"> &mdash; <?php echo getlocal('settings.show_online_operators.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="enablecaptcha" class="flabel"><?php echo getlocal('settings.leavemessage_captcha') ?></label>
			<div class="fvalue">
				<input id="enablecaptcha" type="checkbox" name="enablecaptcha" value="on"<?php echo form_value_cb('enablecaptcha') ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
			</div>
			<label for="enablecaptcha" class="fdescr"> &mdash; <?php echo getlocal('settings.leavemessage_captcha.description') ?></label>
			<br clear="all"/>
		</div>

    <?php if($page['canmodify']) { ?>
		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $mibewroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>
    <?php } ?>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<?php 
} /* content */

require_once(dirname(__FILE__).'/inc_main.php');
?>