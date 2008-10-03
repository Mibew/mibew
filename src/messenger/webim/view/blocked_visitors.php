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


<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getlocal("app.title") ?>	- <?php echo getlocal("page_bans.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

 <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" valign="top">
		<h1><?php echo getlocal("page_bans.title") ?></h1>
 </td><td align="right" class="text" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td class="textform"><?php echo getlocal2("menu.operator",array($page['operator'])) ?></td><td class="textform"><img src='<?php echo $webimroot ?>/images/topdiv.gif' width="25" height="15" border="0" alt="|" /></td><td class="textform"><a href="<?php echo $webimroot ?>/operator/index.php" title="<?php echo getlocal("menu.main") ?>"><?php echo getlocal("menu.main") ?></a></td></tr></table></td></tr></table>


	<?php echo getlocal("page_ban.intro") ?>
<br/>
<br/>
<?php if( isset($errors) && count($errors) > 0 ) { ?>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td valign="top"><img src='<?php echo $webimroot ?>/images/icon_err.gif' width="40" height="40" border="0" alt="" /></td>
	    <td width="10"></td>
	    <td class="text">
		    <?php	if( isset($errors) && count($errors) > 0 ) {
		print getlocal("errors.header");
		foreach( $errors as $e ) {
			print getlocal("errors.prefix");
			print $e;
			print getlocal("errors.suffix");
		}
		print getlocal("errors.footer");
	} ?>

		</td>
		</tr>
		</table>
	<?php } ?>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td class="text"><b><?php echo getlocal("page_bans.list") ?></b></td>
		<td align="right">
		<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><img src="<?php echo $webimroot ?>/images/buttons/createban.gif"
					border="0" alt="<?php echo getlocal("page_bans.add") ?>"></td>
				<td width="10"></td>
				<td class="text"><a href="<?php echo $webimroot ?>/operator/ban.php" title="<?php echo getlocal("page_bans.add") ?>">
						<?php echo getlocal("page_bans.add") ?>
					</a></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="15"></td>
	</tr>
</table>


<?php if( $page['pagination'] && $page['pagination.items'] ) { ?>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'>
				<?php echo getlocal("form.field.address") ?>
			</span></td><td width='3'></td>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'>
				<?php echo getlocal("page_bans.to") ?>
			</span></td><td width='3'></td>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'>
				<?php echo getlocal("form.field.ban_comment") ?>
			</span></td><td width='3'></td>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'>
			</span></td>
		</tr>
		<?php foreach( $page['pagination.items'] as $b ) { ?>
			<tr>
				<td height='45' class='table'>
					<a href="ban.php?id=<?php echo $b['banid'] ?>">
			    	<?php echo htmlspecialchars($b['address']) ?>
			    	</a>
				</td><td background='<?php echo $webimroot ?>/images/tablediv3.gif'><img width='3' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td>
				<td height='45' class='table'>
					<?php echo strftime("%B, %d %Y %H:%M:%S", $b['till']) ?>
				</td><td background='<?php echo $webimroot ?>/images/tablediv3.gif'><img width='3' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td>
				<td height='45' class='table'>
					<?php if( strlen(topage($b['comment'])) > 30 ) { ?>
						<?php echo htmlspecialchars(substr(topage($b['comment']),0,30)) ?>...
					<?php } ?>
					<?php if( strlen(topage($b['comment'])) <= 30 ) { ?>
						<?php echo htmlspecialchars(topage($b['comment'])) ?>
					<?php } ?>
				</td><td background='<?php echo $webimroot ?>/images/tablediv3.gif'><img width='3' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td>
				<td height='45' class='table'>
					<a href="<?php echo $webimroot ?>/operator/blocked.php?act=del&id=<?php echo $b['banid'] ?>">
					<input type="image" name="" src='<?php echo $webimroot.getlocal("image.button.delete") ?>' border="0" alt='<?php echo getlocal("button.delete") ?>'/>
					</a>
				</td>
			</tr>
			<tr><td height='2' colspan='9'></td></tr><tr><td bgcolor='#e1e1e1' colspan='9'><img width='1' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td></tr><tr><td height='2' colspan='9'></td></tr>
		<?php } ?>
	</table>
	<br />
	<?php echo generate_pagination($page['pagination']) ?>
<?php } ?>
<?php if( $page['pagination'] && !$page['pagination.items'] ) { ?>
	<table cellspacing='0' cellpadding='0' border='0'><tr><td background='<?php echo $webimroot ?>/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0'>
		<span class="table"> <?php echo getlocal("tag.pagination.no_items.elements") ?> </span>
	</table></td><td></td></tr><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>
<?php } ?>

</td>
</tr>
</table>

</body>
</html>

