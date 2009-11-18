			<li>
				<h2><?php echo getlocal("submenu.title") ?></h2>
				<ul id="submenu">
					<li<?php echo $subpage == 'news' ? ' class="active"' : '' ?>><a href="index.php"><?php echo getlocal("menu.news") ?></a></li>
					<li<?php echo $subpage == 'contacts' ? ' class="active"' : '' ?>><a href="contact.php"><?php echo getlocal("menu.contacts") ?></a></li>
					<li<?php echo $subpage == 'terms' ? ' class="active"' : '' ?>><a href="license.php"><?php echo getlocal("menu.terms") ?></a></li>
					<li<?php echo $subpage == 'copywrite' ? 'class="active" ' : '' ?>><a href=copywrite.php><?php echo getlocal("menu.copywrite") ?> </a></li>
                                        <li<?php echo $subpage == 'credits' ? ' class="active"' : '' ?>><a href="credits.php"><?php echo getlocal("menu.credits") ?></a></li>
				</ul>
			</li>
