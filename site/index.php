<?php 
function handle_error($errno, $errstr) {
    echo <<<END
<html>
<head>
    <title>Mibew.org :: Woops!</title>
    <style type='text/css'>
        div#header {
            width: 100%;
            position: absolute;
            top: 0px;
            left: 0px;
            height: 10px;
            background-color: #00567a;
        }
        body {
            text-align: center;
        }
        .logo-text {
            font-family: Verdana, sans-serif;
            font-size: 40px;
            color: #555555;
            text-shadow: 0px -1px 0px #111111;
        }
        .woops {
            font-size: 20px;
            font-weight: bold;
            font-family: Arial, serif;
        }
    </style>
</head>
<body>
    <br /><br /><br /><br />
    <div id='header'></div>
    <img src='http://www.burn-blue.com/image/view/r03JwQ8l/64x64.gif' alt='Mibew Logo' title='Mibew Logo' />&nbsp;<span class='logo-text'>Mibew</span>
    <br /><br /><br />
    <span class='woops'>Woops! We seem to be having problems right now, check back in about 10 minutes by which time we hope to have the problem fixed</span>
</body>
</html>
END;
}

set_error_handler('handle_error', E_ALL & ~E_STRICT);

$page = 'home';
$subpage = 'news';
require_once('libs/common.php');
start_html_output();
$title = getlocal("home.title");
require_once('inc/header.i');
require_once('inc/menu.i');

?>

<div id="page">
	<!-- start content -->
	<div id="content">
		<div class="box1">
			<p><img src="images/webimlogo.gif" alt="" width="74" height="79" class="left" /><?php echo getlocal("head.intro") ?></p>
		</div>
		<div class="post">
			<h2 class="title"><?php echo getlocal("index.how.title") ?></h2>
			<div class="entry">
				<p><?php echo getlocal("index.how.text") ?></p>

			</div>
			<div class="nometa"></div>
		</div>
<?php /*
    		<div class="post">
			<h2 class="title"><?php echo getlocal("index.nextpost.title") ?></h2>
			<div class="entry">
				<?php echo getlocal("index.nextpost.text") ?>
			</div>
			<div class="meta">
				<p class="byline"><?php echo getlocal("index.nextpost.when") ?></p>
				<p class="links"><?php echo getlocal("index.nextpost.link") ?></p>
			</div>
		</div> */ ?>
		<div class="post">
			<h2 class="title"><?php echo getlocal("index.post.title") ?></h2>
			<div class="entry">
				<?php echo getlocal("index.post.text") ?>
			</div>
			<div class="meta">
				<p class="byline"><?php echo getlocal("index.post.when") ?></p>
				<p class="links"><?php echo getlocal("index.post.link") ?></p>
			</div>
		</div>
	</div>
	<!-- end content -->
	<!-- start sidebar -->
	<div id="sidebar">
		<ul>
<?php
require_once('inc/main.i');
?>
			<li>
				<h2><?php echo getlocal("sidebar.quicknav") ?></h2>
				<ul>
					<li><a href="features.php"><?php echo getlocal("menu.features") ?></a></li>
				</ul>
			</li>
<?php
require_once('inc/locales.i');
?>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>

<?php require_once('inc/footer.i'); ?>
