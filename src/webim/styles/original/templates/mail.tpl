<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:chat.window.title.user}</title>
<link rel="shortcut icon" href="${mibewroot}/images/favicon.ico" type="image/x-icon"/>
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
.ierricon { background-position:0px 0px; width: 40px; height: 40px; }
.iback { background-position:-41px -30px; width: 25px; height: 26px; }
.isubmit { background-position:0px -39px; width: 40px; height: 35px; }
.isubmitrest { background-position:-31px -39px; width: 10px; height: 35px;}
</style>
</head>
<body bgcolor="#FFFFFF" style="background-image: url(${tplroot}/images/bg.gif); margin: 0px;" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400">
<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

<form name="mailThreadForm" method="post" action="${mibewroot}/mail.php"><input type="hidden" name="style" value="${styleid}"/>
<input type="hidden" name="thread" value="${page:ct.chatThreadId}"/><input type="hidden" name="token" value="${page:ct.token}"/><input type="hidden" name="level" value="${page:level}"/>

<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0" border="0">
<tr>
<td height="75"></td>
<td class="window">
	<h1>${msg:mailthread.title}</h1>
</td>
<td></td>
</tr>
<tr><td></td>
<td height="25">
${if:errors}
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td valign="top"><img class="tplimage ierricon" src="${mibewroot}/images/free.gif" border="0" alt=""/></td>
	    <td width="10"></td>
	    <td class="text">
		    ${errors}
		</td>
		</tr>
		</table>
${endif:errors}
</td><td></td>
</tr>

<tr>
<td height="60"></td>
<td>

	<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td width="15"><img class="tplimage icrnlt" src="${mibewroot}/images/free.gif" border="0" alt=""/></td><td width="100%" style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="15"><img class="tplimage icrnrt" src="${mibewroot}/images/free.gif" border="0" alt=""/></td></tr><tr><td height="100%" bgcolor="#FED840"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy"><table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td class="text">${msg:mailthread.enter_email}</td>
	    <td width="10"></td>
	    <td><input type="text" name="email" size="20" value="${form:email}" class="field"/></td>
		</tr>
	</table></td><td bgcolor="#E8A400"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td><img class="tplimage icrnlb" src="${mibewroot}/images/free.gif" border="0" alt=""/></td><td style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td><img class="tplimage icrnrb" src="${mibewroot}/images/free.gif" border="0" alt=""/></td></tr></table>

</td>
<td></td>
</tr>

<tr>
<td height="70"></td>
<td>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="50%">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:document.mailThreadForm.submit();" title="${msg:mailthread.perform}"><img class="tplimage isubmit" src="${mibewroot}/images/free.gif" border="0" alt=""/></a></td>
	    <td style="background-image: url(${mibewroot}/images/submitbg.gif)" valign="top" class="submit">
			<img src="${mibewroot}/images/free.gif" width="1" height="10" border="0" alt="" /><br/>
			<a href="javascript:document.mailThreadForm.submit();" title="${msg:mailthread.perform}">${msg:mailthread.perform}</a><br/>
		</td>
	    <td width="10"><a href="javascript:document.mailThreadForm.submit();" title="${msg:mailthread.perform}"><img class="tplimage isubmitrest" src="${mibewroot}/images/free.gif" border="0" alt=""/></a></td>
		</tr>
		</table>
	</td>
    <td width="50%" align="right">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:window.close();" title="${msg:mailthread.close}"><img class="tplimage iback" src="${mibewroot}/images/free.gif" border="0" alt=""/></a></td>
	    <td width="5"></td>
	    <td class="button"><a href="javascript:window.close();" title="${msg:mailthread.close}">${msg:mailthread.close}</a></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>

</td>
<td></td>
</tr>

<tr>
<td width="30"><img src="${mibewroot}/images/free.gif" width="30" height="1" border="0" alt="" /></td>
<td width="100%"><img src="${mibewroot}/images/free.gif" width="540" height="1" border="0" alt="" /></td>
<td width="30"><img src="${mibewroot}/images/free.gif" width="30" height="1" border="0" alt="" /></td>
</tr>
</table>

</form>



</td>
</tr>
</table>
</body>
</html>

