<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
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

function print_tabbar($maxwidth = 4) {
	global $page;
	
	if($page['tabs']) {
		$tabbar = $page['tabs'];
		$len = count($tabbar);
		$selected = $page['tabselected'];
		$tabbar2 = array();
		for($i = 0; $i < $len; $i++) {
			$tabbar2[] = $i != $selected
				? "<li><a href=\"".$tabbar[$i]['link']."\">".$tabbar[$i]['title']."</a></li>\n"
				: "<li class=\"active\"><a href=\"#\">".$tabbar[$i]['title']."</a></li>\n";
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