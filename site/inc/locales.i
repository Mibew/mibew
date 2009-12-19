			<li>
				<h2><?php echo getlocal("partners.title") ?></h2>
				<ul>
				<li><a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=195701&amp;type=2" width="125" height="37" alt="SourceForge.net Logo" /></a></li>
				<li><a href="http://www.trilexnet.com/" style="padding-left:20px;"><img src="http://www.trilexnet.com/images/trilexlabs.jpg" width="80" height="30" alt="Trilex Labs Logo"/></a></li>
				<!--
				<li><a href="http://www.mediacms.net/" style="padding-left:10px;"><img src="images/mediacms.png" width="88" height="37"/></a></li> -->
				</ul>
			</li>
<?php if(!(isset($hidelocales) && $hidelocales)) { ?>
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
<?php } ?>
			<li>
				<h2><b>Stay in Touch</b></h2>
				<ul>
				<li><a href="http://twitter.com/mibew"><img src="/images/twitterbutton-0208.png" title="By: TwitterButtons.com" width="120" height="90" alt="Follow us"/></a></li>
				<li>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><p>
<input type="hidden" name="cmd" value="_s-xclick"/>
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----"/>
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!"/>
<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
</p></form>

				</li>
				</ul>
			</li>
			