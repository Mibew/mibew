<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>${msg:leavemessage.title}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${webimroot}/chat.css" />
</head>
<style>
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
<body bgcolor="#FFFFFF" background="${tplroot}/images/bg.gif" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" marginwidth="0" marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

<form name="leaveMessageForm" method="post" action="${webimroot}/leavemessage.php">
<input type="hidden" name="style" value="${styleid}"/>
<input type="hidden" name="info" value="${page:info}"/>
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
	    <td valign="top"><img class="tplimage ierricon" src="${webimroot}/images/free.gif" border="0" alt=""/></td>
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

	<table cellspacing="0" cellpadding="0" border="0"><tr><td width="15"><img class="tplimage icrnlt" src="${webimroot}/images/free.gif" border="0" alt=""/></td><td width="100%" background="${tplroot}/images/winbg.gif" class="bgcy"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="15"><img class="tplimage icrnrt" src="${webimroot}/images/free.gif" border="0" alt=""/></td></tr><tr><td height="100%" bgcolor="#FED840"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td background="${tplroot}/images/winbg.gif" class="bgcy"><table cellspacing="0" cellpadding="0" border="0">
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
	</table></td><td bgcolor="#E8A400"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td><img class="tplimage icrnlb" src="${webimroot}/images/free.gif" border="0" alt=""/></td><td background="${tplroot}/images/winbg.gif" class="bgcy"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td><img class="tplimage icrnrb" src="${webimroot}/images/free.gif" border="0" alt=""/></td></tr></table>

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
	    <td><a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}"><img class="tplimage isubmit" src="${webimroot}/images/free.gif" border="0" alt=""/></a></td>
	    <td background="${webimroot}/images/submitbg.gif" valign="top" class="submit">
			<img src='${webimroot}/images/free.gif' width="1" height="10" border="0" alt="" /><br>
			<a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}">${msg:leavemessage.perform}</a><br>
		</td>
	    <td width="10"><a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}"><img class="tplimage isubmitrest" src="${webimroot}/images/free.gif" border="0" alt=""/></a></td>
		</tr>
		</table>
	</td>

  	<td width="33%" align="center" class="copyr">${msg:chat.window.poweredby} <a href="${msg:site.url}" title="${msg:company.title}" target="_blank">${msg:chat.window.poweredreftext}</a></td>

    <td width="33%" align="right">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:window.close();" title="${msg:leavemessage.close}"><img class="tplimage iback" src="${webimroot}/images/free.gif" border="0" alt=""/></a></td>
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
