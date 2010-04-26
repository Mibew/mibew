			<li>
				<h2><?php echo getlocal("submenu.title") ?></h2>
				<ul id="submenu">
					<li<?php echo $subpage == 'news' ? ' class="active"' : '' ?>><a href="<?php echo $siteroot ?>/index.php"><?php echo getlocal("menu.news") ?></a></li>
					<li<?php echo $subpage == 'contacts' ? ' class="active"' : '' ?>><a href="<?php echo $siteroot ?>/contact.php"><?php echo getlocal("menu.contacts") ?></a></li>
					<li<?php echo $subpage == 'terms' ? ' class="active"' : '' ?>><a href="<?php echo $siteroot ?>/license.php"><?php echo getlocal("menu.terms") ?></a></li>
					<li<?php echo $subpage == 'copyright' ? ' class="active"' : '' ?>><a href="<?php echo $siteroot ?>/copyright.php"><?php echo getlocal("menu.copyright") ?></a></li>
                	<li<?php echo $subpage == 'credits' ? ' class="active"' : '' ?>><a href="<?php echo $siteroot ?>/credits.php"><?php echo getlocal("menu.credits") ?></a></li>
				</ul>
			</li>
