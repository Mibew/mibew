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

function print_tabbar($maxwidth = 4) {
	global $page;

	if($page['tabs']) {
		$tabbar = $page['tabs'];
		$len = count($tabbar);
		$selected = $page['tabselected'];
		$tabbar2 = array();
		for($i = 0; $i < $len; $i++) {
			$tabbar2[] = $i != $selected
				? "<li><a href=\"" . safe_htmlspecialchars($tabbar[$i]['link']) . "\">" . safe_htmlspecialchars($tabbar[$i]['title']) . "</a></li>\n"
				: "<li class=\"active\"><a href=\"#\">" . safe_htmlspecialchars($tabbar[$i]['title']) . "</a></li>\n";
		}

		if($len > $maxwidth) { // && $len - $selected > $maxwidth
			if($selected < $maxwidth) {
				$tabbar = array_splice($tabbar2, 0, $maxwidth);
				array_splice($tabbar2, count($tabbar2),0, $tabbar);
			} // else 3 rows menu
		}		

		echo "<ul class=\"tabs\">\n";
		$i = 0;
		foreach($tabbar2 as $v) {
			if($i > 0 && (($len-$i)%$maxwidth) == 0) {
				echo "</ul><br clear=\"all\"><ul class=\"tabs\">\n";
			}
			echo $v;
			$i++;
		}
		echo "</ul>";
	}
}

?>