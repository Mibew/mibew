<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>${msg:leavemessage.title}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${webimroot}/chat.css" />
</head>
<body bgcolor="#FFFFFF" background="${webimroot}/images/bg.gif" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" marginwidth="0" marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

<form name="leaveMessageForm" method="post" action="${webimroot}/leavemessage.php">

<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr><td colspan="3" height="15"></td></tr>
<tr>
<td height="40"></td>
<td class="window">
	<h1>${msg:leavemessage.title}</h1>
</td>
<td></td>
</tr>
<tr><td></td>
<td height="25">
${if:errors}
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td valign="top"><img src='${webimroot}/images/icon_err.gif' width="40" height="40" border="0" alt="" /></td>
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

	<table cellspacing="0" cellpadding="0" border="0"><tr><td width="15"><img src="${webimroot}/images/wincrnlt.gif" width="15" height="15" border="0" alt="" /></td><td width="100%" background="${webimroot}/images/winbg.gif" class="bgcy"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="15"><img src="${webimroot}/images/wincrnrt.gif" width="15" height="15" border="0" alt="" /></td></tr><tr><td height="100%" bgcolor="#FED840"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td background="${webimroot}/images/winbg.gif" class="bgcy"><table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td colspan="3" class="text">${msg:leavemessage.descr}</td>
		</tr>
		<tr><td height="20" colspan="3"></td></tr>
		<tr>
	    <td class="text">${msg:form.field.email}:</td>
	    <td width="20"></td>
	    <td><input type="text" name="email" size="50" value="${form:email}" class="username"/></td>
		</tr>
		<tr><td height="7" colspan="3"></td></tr>
		<tr>
	    <td class="text">${msg:form.field.name}:</td>
	    <td width="20"></td>
	    <td><input type="text" name="name" size="50" value="${form:name}" class="username"/></td>
		</tr>
		<tr><td height="7" colspan="3"></td></tr>
		<tr>
	    <td class="text">${msg:form.field.message}:</td>
	    <td width="20"></td>
	    <td height="120" valign="top"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0"><tr><td colspan="3" bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
	    <textarea name="message" class="message" tabindex="0">${form:message}</textarea>
	    </td><td bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td colspan="3" bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr></table></td>
		</tr>
	</table></td><td bgcolor="#E8A400"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td><img src="${webimroot}/images/wincrnlb.gif" width="15" height="15" border="0" alt="" /></td><td background="${webimroot}/images/winbg.gif" class="bgcy"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td><img src="${webimroot}/images/wincrnrb.gif" width="15" height="15" border="0" alt=""/></td></tr></table>

</td>
<td></td>
</tr>

<tr>
<td height="70"></td>
<td>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="33%">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}"><img src='${webimroot}/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
	    <td background="${webimroot}/images/submitbg.gif" valign="top" class="submit">
			<img src='${webimroot}/images/free.gif' width="1" height="10" border="0" alt="" /><br>
			<a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}">${msg:leavemessage.perform}</a><br>
		</td>
	    <td width="10"><a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}"><img src='${webimroot}/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
		</tr>
		</table>
	</td>

  	<td width="33%" align="center" class="copyr">${msg:chat.window.poweredby} <a href="${msg:site.url}" title="${msg:company.title}" target="_blank">${msg:chat.window.poweredreftext}</a></td>

    <td width="33%" align="right">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:window.close();" title="${msg:leavemessage.close}"><img src='${webimroot}/images/buttons/back.gif' width="25" height="25" border="0" alt="" /></a></td>
	    <td width="5"></td>
	    <td class="button"><a href="javascript:window.close();" title="${msg:leavemessage.close}">${msg:leavemessage.close}</a></td>
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

</form>

</td>
</tr>
</table>
</body>
</html>
