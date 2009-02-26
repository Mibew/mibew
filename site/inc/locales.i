			<li>
				<h2><b>partners</b></h2>
				<ul>
				<li><a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=195701&amp;type=2" width="125" height="37" border="0" alt="SourceForge.net Logo" /></a></li>
				</ul>
			</li>
			<li id="locales">
				<h2><b>languages</b></h2>
				<p>
<?php	
	foreach(array('en'=>'English','sp'=>'Spanish','ru'=>'Russian') as $k => $v) {
		if($k == $current_locale) {
			echo "<a href=\"#\" class=\"inactive\">".$v."</a>";
		} else {
			echo "<a href=\"?locale=$k\">".$v."</a>";
		}
	}			
?>
				</p>
			</li>
			