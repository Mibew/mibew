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

$page['title'] = getlocal("thread.chat_log");

function tpl_content() { global $page, $mibewroot, $errors;
$chatthread = isset($page['thread']) ? $page['thread'] : array();
?>

<?php echo getlocal("thread.intro") ?>

<br/><br/>

<div class="logpane">
<div class="header">

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_name") ?>:
		</div> 
		<div class="wvalue">
			<?php echo topage(safe_htmlspecialchars(isset($chatthread['userName']) ? $chatthread['userName'] : '')) ?>
		</div>
		<br clear="all"/>
		
		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_host") ?>:
		</div>
		<div class="wvalue">
			<?php echo get_user_addr(topage(isset($chatthread['remote']) ? $chatthread['remote'] : '')) ?>
		</div>
		<br clear="all"/>

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_browser") ?>:
		</div>
		<div class="wvalue">
			<?php echo get_useragent_version(topage(isset($chatthread['userAgent']) ? $chatthread['userAgent'] : '')) ?>
		</div>
		<br clear="all"/>

		<?php if( isset($chatthread['groupName']) && $chatthread['groupName'] ) { ?>
			<div class="wlabel">
				<?php echo getlocal("page.analysis.search.head_group") ?>:
			</div>
			<div class="wvalue">
				<?php echo topage(safe_htmlspecialchars($chatthread['groupName'])) ?>
			</div>
			<br clear="all"/>
		<?php } ?>

		<?php if( isset($chatthread['agentName']) && $chatthread['agentName'] ) { ?>
			<div class="wlabel">
				<?php echo getlocal("page.analysis.search.head_operator") ?>:
			</div>
			<div class="wvalue">
				<?php echo topage(safe_htmlspecialchars($chatthread['agentName'])) ?>
			</div>
			<br clear="all"/>
		<?php } ?>

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_time") ?>:
		</div>
		<div class="wvalue">
			<?php echo date_diff_to_text((isset($chatthread['modified']) ? $chatthread['modified'] : 0) - (isset($chatthread['created']) ? $chatthread['created'] : 0)) ?> 
				(<?php echo date_to_text(isset($chatthread['created']) ? $chatthread['created'] : 0) ?>)
		</div>
		<br clear="all"/>
</div>

<div class="message">
<?php 
	if (isset($page['threadMessages']) && is_array($page['threadMessages'])) {
		foreach( $page['threadMessages'] as $message ) {
			echo $message;
		}
	}
?>
</div>
</div>

<br />
<a href="<?php echo $mibewroot ?>/operator/history.php">
	<?php echo getlocal("thread.back_to_search") ?></a>
<br />


<?php
} /* content */

require_once('inc_main.php');
?>