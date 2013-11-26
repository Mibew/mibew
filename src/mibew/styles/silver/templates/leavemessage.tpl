<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>${msg:leavemessage.title}</title>
	<link rel="shortcut icon" href="${mibewroot}/images/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body class="bgbody">

	<form name="leaveMessageForm" method="post" action="${mibewroot}/leavemessage.php">
	<input type="hidden" name="style" value="${styleid}"/>
	<input type="hidden" name="info" value="${form:info}"/>
	<input type="hidden" name="referrer" value="${page:referrer}"/>
	${if:formgroupid}<input type="hidden" name="group" value="${form:groupid}"/>${endif:formgroupid}

	<div id="top2">
		<div id="logo">
			${if:ct.company.chatLogoURL}
				${if:mibewHost}
					<a onclick="window.open('${page:mibewHost}');return false;" href="${page:mibewHost}">
						<img src="${page:ct.company.chatLogoURL}" alt=""/>
					</a>
				${else:mibewHost}
					<img src="${page:ct.company.chatLogoURL}" alt=""/>
				${endif:mibewHost}
			${else:ct.company.chatLogoURL}
				${if:mibewHost}
					<a onclick="window.open('${page:mibewHost}');return false;" href="${page:mibewHost}">
						<img src="${tplroot}/images/default-logo.png" alt=""/>
					</a>
				${else:mibewHost}
					<img src="${tplroot}/images/default-logo.png" alt=""/>
				${endif:mibewHost}
			${endif:ct.company.chatLogoURL}
			&nbsp;
			<div id="page-title">${if:formgroupname}${form:groupname}: ${endif:formgroupname}${msg:leavemessage.title}</div>
			<div class="clear">&nbsp;</div>
		</div>
	</div>
	<div id="headers">
		<div class="wndb"><div class="wndl"><div class="wndr"><div class="wndt"><div class="wndtl"><div class="wndtr"><div class="wndbl"><div class="wndbr">
			<div class="buttons">
				<a href="javascript:window.close();" title="${msg:leavemessage.close}"><img class="tplimage iclosewin" src="${mibewroot}/images/free.gif" alt="${msg:leavemessage.close}" /></a>
			</div>
			<div class="messagetxt">${msg:leavemessage.descr}</div>
		</div></div></div></div></div></div></div></div>
	</div>
	<div id="content-wrapper">
		${if:errors}
			${errors}
		${endif:errors}
		<table cellspacing="1" cellpadding="5" border="0" class="form">
			<tr>
				<td><strong>${msg:form.field.email}:</strong></td>
				<td><input type="text" name="email" size="50" value="${form:email}" class="username"/></td>
			</tr>
			<tr>
				<td><strong>${msg:form.field.name}:</strong></td>
				<td><input type="text" name="name" size="50" value="${form:name}" class="username"/></td>
			</tr>
			<tr>
				<td><strong>${msg:form.field.message}:</strong></td>
				<td valign="top">
					<textarea name="message" tabindex="0" cols="40" rows="5">${form:message}</textarea>
				</td>
			</tr>
			${if:showcaptcha}
				<tr>
					<td><img src="captcha.php"/></td>
					<td><input type="text" name="captcha" size="50" maxlength="15" value="" class="username"/></td>
				</tr>
			${endif:showcaptcha}
		</table>
		<a href="javascript:document.leaveMessageForm.submit();" class="but" id="sndmessagelnk">${msg:mailthread.perform}</a>
		<div class="clear">&nbsp;</div>
	</div>
	</form>
</body>
</html>
