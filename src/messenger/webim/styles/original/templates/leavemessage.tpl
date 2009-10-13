<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:leavemessage.title}</title>
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

<form name="leaveMessageForm" method="post" action="${webimroot}/leavemessage.php">
<input type="hidden" name="style" value="${styleid}"/>
<input type="hidden" name="info" value="${page:info}"/>
<input type="hidden" name="referrer" value="${page:referrer}"/>
${if:formgroupid}<input type="hidden" name="group" value="${form:groupid}"/>${endif:formgroupid}
<table width="100%" style="height:100%;" cellspacing="0" cellpadding="0" border="0">
<tr><td colspan="3" height="15"></td></tr>
<tr>
<td height="40"></td>
<td class="window">
	<h1>${if:formgroupname}${form:groupname}: ${endif:formgroupname}${msg:leavemessage.title}</h1>
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

	<table cellspacing="0" cellpadding="0" border="0"><tr><td width="15"><img class="tplimage icrnlt" src="${webimroot}/images/free.gif" border="0" alt=""/></td><td width="100%" style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="15"><img class="tplimage icrnrt" src="${webimroot}/images/free.gif" border="0" alt=""/></td></tr><tr><td height="100%" bgcolor="#FED840"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy"><table cellspacing="0" cellpadding="0" border="0">
		<tr>
		    <td colspan="3" class="text">${msg:leavemessage.descr}</td>
		</tr>
		<tr><td height="20" colspan="3"></td></tr>
		<tr>
		    <td class="text">${msg:form.field.email}:</td>
		    <td width="20"></td>
		    <td><input type="text" name="email" size="50" value="${form:email}" class="field"/></td>
		</tr>
		<tr><td height="7" colspan="3"></td></tr>
		<tr>
		    <td class="text">${msg:form.field.name}:</td>
		    <td width="20"></td>
		    <td><input type="text" name="name" size="50" value="${form:name}" class="field"/></td>
		</tr>
		<tr><td height="7" colspan="3"></td></tr>
		<tr>
		    <td class="text">${msg:form.field.message}:</td>
		    <td width="20"></td>
		    <td height="120" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td colspan="3" bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
			    <textarea rows="8" cols="45" name="message" class="field" tabindex="0">${form:message}</textarea>
		    </td><td bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td colspan="3" bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr></table></td>
		</tr>
${if:showcaptcha}
		<tr><td height="7" colspan="3"></td></tr>
		<tr>
		    <td class="text"><img src="captcha.php"/></td>
		    <td width="20"></td>
			<td><input type="text" name="captcha" size="50" maxlength="15" value="" class="field"/></td>
		</tr>
${endif:showcaptcha}
	</table></td><td bgcolor="#E8A400"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td><img class="tplimage icrnlb" src="${webimroot}/images/free.gif" border="0" alt=""/></td><td style="background-image: url(${tplroot}/images/winbg.gif)" class="bgcy"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td><img class="tplimage icrnrb" src="${webimroot}/images/free.gif" border="0" alt=""/></td></tr></table>

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
	    <td style="background-image: url(${webimroot}/images/submitbg.gif)" valign="top" class="submit">
			<img src='${webimroot}/images/free.gif' width="1" height="10" border="0" alt="" /><br/>
			<a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}">${msg:leavemessage.perform}</a><br/>
		</td>
	    <td width="10"><a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}"><img class="tplimage isubmitrest" src="${webimroot}/images/free.gif" border="0" alt=""/></a></td>
		</tr>
		</table>
	</td>

  	<td width="33%" align="center" class="copyr">${msg:chat.window.poweredby} <a id="poweredByLink" href="http://mibew.org" title="Mibew Community" target="_blank">mibew.org</a></td>

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
