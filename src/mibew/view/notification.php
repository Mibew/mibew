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

$page['title'] = getlocal("notification.title");

function tpl_content() { global $page, $mibewroot, $errors;
$notification = $page['notification'];
?>

<?php echo getlocal("notification.intro") ?>

<br/><br/>

<div class="logpane">
<div class="header">

		<div class="wlabel">
			<?php echo getlocal("notification.label.to") ?>:
		</div> 
		<div class="wvalue">
			<?php echo topage(safe_htmlspecialchars($notification['vcto'])) ?>
		</div>
		<br clear="all"/>

		<div class="wlabel">
			<?php echo getlocal("notification.label.time") ?>:
		</div>
		<div class="wvalue">
			<?php echo date_to_text($notification['created']) ?>
		</div>
		<br clear="all"/>
		
		<div class="wlabel">
			<?php echo getlocal("notification.label.subj") ?>:
		</div>
		<div class="wvalue">
			<?php echo topage(safe_htmlspecialchars($notification['vcsubject'])) ?>
		</div>
		<br clear="all"/>
</div>

<div class="message">
<?php echo topage(prepare_html_message($notification['tmessage'])) ?>
</div>
</div>

<br />
<a href="<?php echo $mibewroot ?>/operator/notifications.php">
	<?php echo getlocal("notification.back_to_list") ?></a>
<br />


<?php
} /* content */

require_once('inc_main.php');
?>