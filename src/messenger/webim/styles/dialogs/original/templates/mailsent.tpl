<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:chat.window.title.user}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
<style type="text/css">
.tplimage {
	background: transparent url(${tplroot}/images/wmfrm.png) no-repeat scroll 0px 0px;
	-moz-background-clip: -moz-initial; 
	-moz-background-origin: -moz-initial; 
	-moz-background-inline-policy: -moz-initial;
}
.icrnlb { background-position:-40px -15px; width: 15px; height: 15px; }
.icrnlt { background-position:-40px 0px; width: 15px; height: 15px; }
.icrnrb { background-position:-55px -15px; width: 15px; height: 15px; }
.icrnrt { background-position:-55px 0px; width: 15px; height: 15px; }
.iback { background-position:-41px -30px; width: 25px; height: 26px; }
</style>
</head>
<body bgcolor="#FFFFFF" style="background-image: url(${tplroot}/images/bg.gif); margin: 0px;" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400">
<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0" border="0">
<tr>
<td height="75"></td>
<td class="window">
	<h1>${msg:chat.mailthread.sent.title}</h1>
</td>
<td></td>
</tr>

<tr>
<td height="100%"></td>
<td>

	<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="15"><img class="tplimage icrnlt" src="${webimroot}/images/free.gif" border="0" alt=""/></td>
	<td width="100%" style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
    <td width="15"><img class="tplimage icrnrt" src="${webimroot}/images/free.gif" border="0" alt=""/></td>
	</tr>

	<tr>
    <td height="100%" bgcolor="#FED840"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
	<td style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy">

		${msg:chat.mailthread.sent.content,email}<br/>
		<a href="javascript:window.close();">${msg:chat.mailthread.sent.closewindow}</a>

	</td>
    <td bgcolor="#E8A400"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
	</tr>

	<tr>
    <td><img class="tplimage icrnlb" src="${webimroot}/images/free.gif" border="0" alt=""/></td>
	<td style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
    <td><img class="tplimage icrnrb" src="${webimroot}/images/free.gif" border="0" alt=""/></td>
	</tr>
	</table>

</td>
<td></td>
</tr>

<tr>
<td height="70"></td>
<td>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="100%" align="right">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:window.close();" title="${msg:chat.mailthread.sent.close}"><img class="tplimage iback" src="${webimroot}/images/free.gif" border="0" alt=""/></a></td>
	    <td width="5"></td>
	    <td class="button"><a href="javascript:window.close();" title="${msg:chat.mailthread.sent.close}">${msg:chat.mailthread.sent.close}</a></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>

</td>
<td></td>
</tr>

<tr>
<td width="30"><img src='${webimroot}/images/free.gif' width="30" height="1" border="0" alt="" /></td>
<td width="100%"><img src='${webimroot}/images/free.gif' width="540" height="1" border="0" alt="" /></td>
<td width="30"><img src='${webimroot}/images/free.gif' width="30" height="1" border="0" alt="" /></td>
</tr>
</table>

</td>
</tr>
</table>
</body>
</html>

