<div id="header">

	<div id="clocale">
	 <span><a href="/en">en</a> | <a href="/ru">ru</a> | <a href="/sp">sp</a>
	</div>
	<br clear="all"/>
	<div id="logo">
		
		<h1><a href="#"><?php echo getlocal("head.name") ?></a></h1>
		<h2><?php echo getlocal("head.descr") ?></h2>
	</div>
	<div id="menu">
	  <ul>
        <li<?php echo $page == 'home' ? ' class="active"' : '' ?>><a href="index.php"><?php echo getlocal("menu.home") ?></a></li>
        <li<?php echo $page == 'demo' ? ' class="active"' : '' ?>><a href="demo.php"><?php echo getlocal("menu.demo") ?></a></li>
        <li<?php echo $page == 'downl' ? ' class="active"' : '' ?>><a href="download.php"><?php echo getlocal("menu.download") ?></a></li>
        <li><a href="contact.php"><?php echo getlocal("menu.ticket") ?></a></li>
        <li><a href="/forums/"><?php echo getlocal("menu.support") ?></a></li>
	  </ul>
	</div>
</div>
