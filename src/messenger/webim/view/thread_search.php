<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
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
	<?php echo getlocal("page_analysis.search.title") ?> - <?php echo getlocal("app.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

 <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" valign="top">
		<h1><?php echo getlocal("page_analysis.search.title") ?></h1>
 </td><td align="right" class="text" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td class="textform"><?php echo getlocal2("menu.operator",array($page['operator'])) ?></td><td class="textform"><img src='<?php echo $webimroot ?>/images/topdiv.gif' width="25" height="15" border="0" alt="|" /></td><td class="textform"><a href="<?php echo $webimroot ?>/operator/index.php" title="<?php echo getlocal("menu.main") ?>"><?php echo getlocal("menu.main") ?></a></td></tr></table></td></tr></table>


	<?php echo getlocal("page_search.intro") ?>
<br />
<br />




<form name="searchForm" method="get" action="<?php echo $webimroot ?>/operator/history.php">
<table cellspacing='0' cellpadding='0' border='0'><tr><td background='<?php echo $webimroot ?>/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0'>
	<tr>
		<td class="formauth" colspan="3"><?php echo getlocal("page_analysis.full.text.search") ?></td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td class="formauth"><input type="text" name="q" size="80" value="<?php echo form_value('q') ?>" class="formauth"/></td>
		<td width="10"><img src="<?php echo $webimroot ?>/images/free.gif" width="10" height="1" border="0" alt=""></td>
		<td class="formauth">
			<input type="image" name="" src='<?php echo $webimroot.getlocal("image.button.search") ?>' border="0" alt='<?php echo getlocal("button.search") ?>'/>
		</td>
	</tr>
</table></td><td></td></tr><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>
</form>

<br/>
<?php if( $page['pagination'] && $page['pagination.items'] ) { ?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'><?php echo getlocal("page.analysis.search.head_name") ?></span></td><td width='3'></td>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'><?php echo getlocal("page.analysis.search.head_host") ?></span></td><td width='3'></td>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'><?php echo getlocal("page.analysis.search.head_operator") ?></span></td><td width='3'></td>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'><?php echo getlocal("page.analysis.search.head_messages") ?></span></td><td width='3'></td>
			<td class='table' bgcolor='#276db8' height='30'><span class='header'><?php echo getlocal("page.analysis.search.head_time") ?></span></td>
		</tr>
		<?php foreach( $page['pagination.items'] as $chatthread ) { ?>
			<tr>
				<td height='30' class='table'>
					<a href="<?php echo $webimroot ?>/operator/threadprocessor.php?threadid=<?php echo $chatthread['threadid'] ?>" target="_blank" onclick="this.newWindow = window.open('<?php echo $webimroot ?>/operator/threadprocessor.php?threadid=<?php echo $chatthread['threadid'] ?>', '', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><?php echo topage(htmlspecialchars($chatthread['userName'])) ?></a>
				</td><td background='<?php echo $webimroot ?>/images/tablediv3.gif'><img width='3' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td>
				<td height='30' class='table'>
        			<?php echo get_user_addr(topage($chatthread['remote'])) ?>
				</td><td background='<?php echo $webimroot ?>/images/tablediv3.gif'><img width='3' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td>
				<td height='30' class='table'>
        			<?php if( $chatthread['agentName'] ) { ?><?php echo topage(htmlspecialchars($chatthread['agentName'])) ?><?php } ?>
				</td><td background='<?php echo $webimroot ?>/images/tablediv3.gif'><img width='3' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td>
				<td height='30' class='table'>
        			<?php echo topage(htmlspecialchars($chatthread['size'])) ?>
				</td><td background='<?php echo $webimroot ?>/images/tablediv3.gif'><img width='3' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td>
				<td height='30' class='table'>
					<?php echo date_diff($chatthread['modified']-$chatthread['created']) ?>, <?php echo strftime("%B, %d %Y %H:%M:%S", $chatthread['created']) ?>
				</td>
			</tr>
			<tr><td height='2' colspan='9'></td></tr><tr><td bgcolor='#e1e1e1' colspan='9'><img width='1' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td></tr><tr><td height='2' colspan='9'></td></tr>
		<?php } ?>
	</table>
	<br />
	<?php echo generate_pagination($page['pagination']) ?>
<?php } ?>
<?php if( $page['pagination'] && !$page['pagination.items'] ) { ?>
	<br/><br/>
	<table cellspacing='0' cellpadding='0' border='0'><tr><td background='<?php echo $webimroot ?>/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0'>
		<span class="table">
			<?php echo getlocal("tag.pagination.no_items") ?>
		</span>
	</table></td><td></td></tr><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>
<?php } ?>

</td>
</tr>
</table>

</body>
</html>

