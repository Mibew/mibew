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

function menuloc($id) {
	global $current_locale;
	if($current_locale == $id) {
		echo " class=\"active\"";
	}
	return "";
}
function tpl_menu() { global $page, $mibewroot, $errors, $current_locale;
?>
			<li>
				<h2><b><?php echo getlocal("lang.choose") ?></b></h2>
				<ul class="locales">
<?php foreach($page['localeLinks'] as $id => $title) { ?>
					<li<?php menuloc($id)?> ><a href="?locale=<?php echo urlencode($id) ?>"><?php echo safe_htmlspecialchars($title) ?></a></li>
<?php } ?>
				</ul>
			</li>
<?php
}
?>