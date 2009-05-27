			<li>
				<h2><?php echo getlocal("partners.title") ?></h2>
				<ul>
				<li><a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=195701&amp;type=2" width="125" height="37" alt="SourceForge.net Logo" /></a></li>
				<li><a href="http://www.trilexnet.com/" style="padding-left:20px;"><img src="http://www.trilexnet.com/images/trilexlabs.jpg" width="80" height="30"/></a></li>
				<li><a href="http://www.mediacms.net/" style="padding-left:10px;"><img src="images/mediacms.png" width="88" height="37"/></a></li>
				</ul>
			</li>
			<li id="locales">
				<h2><?php echo getlocal("languages.title") ?></h2>
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
			