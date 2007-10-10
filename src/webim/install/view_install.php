<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
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



<link rel="stylesheet" type="text/css" media="all" href="/webim/styles.css" />


<link rel="shortcut icon" href="/webim/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getstring("app.title") ?>	- <?php echo getstring("installed.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getstring("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getstring("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">
	
		<h1><?php echo getstring("installed.title") ?></h1>
	

	<?php echo getstring("installed.message") ?>
<br/>
<br/>

<a href="/webim/operator/login.php"><?php echo getstring("installed.login_link") ?></a>

<br/>
<br/>

<a href="/webim/LICENSE.html" target="_blank"><?php echo getstring("install.license") ?></a>
</td>
</tr>
</table>

</body>
</html>

