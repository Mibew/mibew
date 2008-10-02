<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */
?>
<html>
<head>



<link rel="stylesheet" type="text/css" media="all" href="<?php echo $webimroot ?>/styles.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $webimroot ?>/chat.css" />


<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getlocal("app.title") ?>	- <?php echo getlocal("thread.chat_log") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

		<h1><?php echo getlocal("thread.chat_log") ?></h1>



<?php echo getlocal("thread.intro") ?>

<br/><br/>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class='table' bgcolor='#276db8' height='30'><span class='header'>
		<?php echo getlocal("thread.chat_log") ?>
	</span></td>
</tr>
<tr>
	<td height='45' class='table'>
		<span class="message">
                        <?php foreach( $page['threadMessages'] as $message ) { ?>
                        	<?php echo $message ?>
                        <?php } ?>
		</span>
	</td>
</tr>
<tr><td height='2' colspan='1'></td></tr><tr><td bgcolor='#e1e1e1' colspan='1'><img width='1' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td></tr><tr><td height='2' colspan='1'></td></tr>
</table>

<br />
<a href="<?php echo $webimroot ?>/operator/history.php">
	<?php echo getlocal("thread.back_to_search") ?></a>
<br />

</td>
</tr>
</table>

</body>
</html>

