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

function tpl_content($page) {
?>

<?php echo getlocal("page_settings.intro") ?>
<br />
<br />
<?php 
require_once(dirname(__FILE__).'/inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("settings.saved") ?></div>
<?php } ?>

<form name="performance" method="post" action="<?php echo MIBEW_WEB_ROOT ?>/operator/performance.php">
<?php print_csrf_token_input() ?>
	<div>
<?php print_tabbar($page['tabs']); ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<label for="onlinetimeout" class="flabel"><?php echo getlocal('settings.onlinetimeout') ?></label>
			<div class="fvalue">
				<input id="onlinetimeout" type="text" name="onlinetimeout" size="40" value="<?php echo form_value($page, 'onlinetimeout') ?>" class="formauth"/>
			</div>
			<label for="onlinetimeout" class="fdescr"> &mdash; <?php echo getlocal('settings.onlinetimeout.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="frequencyoperator" class="flabel"><?php echo getlocal('settings.frequencyoperator') ?></label>
			<div class="fvalue">
				<input id="frequencyoperator" type="text" name="frequencyoperator" size="40" value="<?php echo form_value($page, 'frequencyoperator') ?>" class="formauth"/>
			</div>
			<label for="frequencyoperator" class="fdescr"> &mdash; <?php echo getlocal('settings.frequencyoperator.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="frequencychat" class="flabel"><?php echo getlocal('settings.frequencychat') ?></label>
			<div class="fvalue">
				<input id="frequencychat" type="text" name="frequencychat" size="40" value="<?php echo form_value($page, 'frequencychat') ?>" class="formauth"/>
			</div>
			<label for="frequencychat" class="fdescr"> &mdash; <?php echo getlocal('settings.frequencychat.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="onehostconnections" class="flabel"><?php echo getlocal('settings.onehostconnections') ?></label>
			<div class="fvalue">
				<input id="onehostconnections" type="text" name="onehostconnections" size="40" value="<?php echo form_value($page, 'onehostconnections') ?>" class="formauth"/>
			</div>
			<label for="onehostconnections" class="fdescr"> &mdash; <?php echo getlocal('settings.onehostconnections.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="threadlifetime" class="flabel"><?php echo getlocal('settings.threadlifetime') ?></label>
			<div class="fvalue">
				<input id="threadlifetime" type="text" name="threadlifetime" size="40" value="<?php echo form_value($page, 'threadlifetime') ?>" class="formauth"/>
			</div>
			<label for="threadlifetime" class="fdescr"> &mdash; <?php echo getlocal('settings.threadlifetime.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="statistics_aggregation_interval" class="flabel"><?php echo getlocal('settings.statistics_aggregation_interval') ?></label>
			<div class="fvalue">
				<input id="statistics_aggregation_interval" type="text" name="statistics_aggregation_interval" size="40" value="<?php echo form_value($page, 'statistics_aggregation_interval') ?>" class="formauth"/>
			</div>
			<label for="statistics_aggregation_interval" class="fdescr"> &mdash; <?php echo getlocal('settings.statistics_aggregation_interval.description') ?></label>
			<br clear="all"/>
		</div>

<?php if ($page['enabletracking']) { ?>
		<div class="field">
			<label for="frequencytracking" class="flabel"><?php echo getlocal('settings.frequencytracking') ?></label>
			<div class="fvalue">
				<input id="frequencytracking" type="text" name="frequencytracking" size="40" value="<?php echo form_value($page, 'frequencytracking') ?>" class="formauth"/>
			</div>
			<label for="frequencytracking" class="fdescr"> &mdash; <?php echo getlocal('settings.frequencytracking.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="visitorslimit" class="flabel"><?php echo getlocal('settings.visitorslimit') ?></label>
			<div class="fvalue">
				<input id="visitorslimit" type="text" name="visitorslimit" size="40" value="<?php echo form_value($page, 'visitorslimit') ?>" class="formauth"/>
			</div>
			<label for="visitorslimit" class="fdescr"> &mdash; <?php echo getlocal('settings.visitorslimit.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="invitationlifetime" class="flabel"><?php echo getlocal('settings.invitationlifetime') ?></label>
			<div class="fvalue">
				<input id="invitationlifetime" type="text" name="invitationlifetime" size="40" value="<?php echo form_value($page, 'invitationlifetime') ?>" class="formauth"/>
			</div>
			<label for="invitationlifetime" class="fdescr"> &mdash; <?php echo getlocal('settings.invitationlifetime.description') ?></label>
			<br clear="all"/>
		</div>

		<div class="field">
			<label for="trackinglifetime" class="flabel"><?php echo getlocal('settings.trackinglifetime') ?></label>
			<div class="fvalue">
				<input id="trackinglifetime" type="text" name="trackinglifetime" size="40" value="<?php echo form_value($page, 'trackinglifetime') ?>" class="formauth"/>
			</div>
			<label for="trackinglifetime" class="fdescr"> &mdash; <?php echo getlocal('settings.trackinglifetime.description') ?></label>
			<br clear="all"/>
		</div>
<?php } ?>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo MIBEW_WEB_ROOT . getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>

	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
	
	<div class="asterisk">
		<?php echo getlocal("common.asterisk_explanation") ?>
	</div>

</form>

<?php 
} /* content */

require_once(dirname(__FILE__).'/inc_main.php');
?>