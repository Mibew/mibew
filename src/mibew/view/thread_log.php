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

$page['title'] = getlocal("thread.chat_log");

function tpl_content() { global $page, $mibewroot, $errors;
$chatthread = $page['thread'];
?>

<?php echo getlocal("thread.intro") ?>

<br/><br/>

<div class="logpane">
<div class="header">

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_name") ?>:
		</div> 
		<div class="wvalue">
			<?php echo topage(safe_htmlspecialchars($chatthread['userName'])) ?>
		</div>
		<br clear="all"/>
		
		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_host") ?>:
		</div>
		<div class="wvalue">
			<?php echo get_user_addr(topage($chatthread['remote'])) ?>
		</div>
		<br clear="all"/>

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_browser") ?>:
		</div>
		<div class="wvalue">
			<?php echo get_useragent_version(topage($chatthread['userAgent'])) ?>
		</div>
		<br clear="all"/>

		<?php if( $chatthread['groupName'] ) { ?>
			<div class="wlabel">
				<?php echo getlocal("page.analysis.search.head_group") ?>:
			</div>
			<div class="wvalue">
				<?php echo topage(safe_htmlspecialchars($chatthread['groupName'])) ?>
			</div>
			<br clear="all"/>
		<?php } ?>

		<?php if( $chatthread['agentName'] ) { ?>
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
			<?php echo date_diff_to_text($chatthread['modified']-$chatthread['created']) ?> 
				(<?php echo date_to_text($chatthread['created']) ?>)
		</div>
		<br clear="all"/>
</div>

<div class="message">
<?php 
	foreach( $page['threadMessages'] as $message ) {
		echo $message;
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