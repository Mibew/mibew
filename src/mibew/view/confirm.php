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

$page['title'] = getlocal("confirm.take.head");

function tpl_content() { global $page, $mibewroot;
?>

<div id="confirmpane">
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

		<?php echo getlocal2("confirm.take.message",array(safe_htmlspecialchars($page['user']), safe_htmlspecialchars($page['agent']))) ?><br/><br/>
		<br/>

		<div>
		<table class="nicebutton"><tr>
			<td><a href="<?php echo safe_htmlspecialchars($page['link']) ?>">
				<img src="<?php echo $mibewroot ?>/images/submit.gif" width="40" height="35" border="0" alt="" /></a></td>
			<td class="submit"><a href="<?php echo safe_htmlspecialchars($page['link']) ?>">
				<?php echo getlocal("confirm.take.yes") ?></a></td>
			<td><a href="<?php echo safe_htmlspecialchars($page['link']) ?>">
				<img src="<?php echo $mibewroot ?>/images/submitrest.gif" width="10" height="35" border="0" alt="" /></a></td>
		</tr></table>

		<table class="nicebutton"><tr>
			<td><a href="javascript:window.close();">
				<img src="<?php echo $mibewroot ?>/images/submit.gif" width="40" height="35" border="0" alt="" /></a></td>
			<td class="submit"><a href="javascript:window.close();">
				<?php echo getlocal("confirm.take.no") ?></a></td>
			<td><a href="javascript:window.close();">
				<img src="<?php echo $mibewroot ?>/images/submitrest.gif" width="10" height="35" border="0" alt="" /></a></td>
		</tr></table>
		
		<br clear="all"/>
		</div>
				
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</div>		

<?php 
} /* content */

require_once('inc_main.php');
?>