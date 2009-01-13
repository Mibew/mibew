<div id="header">
<div id="title">
	<h1><?php echo getlocal("head.name") ?></h1>
	<big><?php echo getlocal("head.descr") ?></big>
</div>
<div id="mrow">
<div id="menu">
    <ul id="nav">
        <li id="home"<?php echo $page == 'home' ? ' class="activelink"' : '' ?>><a href="index.php"><?php echo getlocal("menu.home") ?></a></li>
        <li id="who"<?php echo $page == 'feat' ? ' class="activelink"' : '' ?>><a href="features.php"><?php echo getlocal("menu.features") ?></a></li>
        <li id="prod"<?php echo $page == 'demo' ? ' class="activelink"' : '' ?>><a href="demo.php"><?php echo getlocal("menu.demo") ?></a></li>
        <li id="serv"<?php echo $page == 'downl' ? ' class="activelink"' : '' ?>><a href="download.php"><?php echo getlocal("menu.download") ?></a></li>
        <li id="supp"<?php echo $page == 'supp' ? ' class="activelink"' : '' ?>><a href="forums"><?php echo getlocal("menu.support") ?></a></li>
    </ul>
</div>
<div id="logo">
    <a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=195701&amp;type=2" width="125" height="37" border="0" alt="SourceForge.net Logo" /></a>
</div>
<div id="locales">
    <?php echo $current_locale == 'en' ? 'en' : '<a href="?locale=en">en</a>' ?> | <?php echo $current_locale == 'sp' ? 'sp' : '<a href="?locale=sp">sp</a>' ?> | <?php echo $current_locale == 'ru' ? 'ru' : '<a href="?locale=ru">ru</a>' ?>
</div>
</div>
</div>
