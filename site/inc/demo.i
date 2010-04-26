			<li>
				<h2><?php echo getlocal("submenu.title") ?></h2>
				<ul id="submenu">
					<li<?php echo $subpage == 'demo' ? ' class="active"' : '' ?>><a href="<?php echo $siteroot ?>/demo.php"><?php echo getlocal("menu.demo") ?></a></li>
					<li<?php echo $subpage == 'features' ? ' class="active"' : '' ?>><a href="<?php echo $siteroot ?>/features.php"><?php echo getlocal("menu.features") ?></a></li>
				</ul>
			</li>
