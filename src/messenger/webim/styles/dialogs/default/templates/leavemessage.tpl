<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>${msg:leavemessage.title}</title>
	<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
	<script type="text/javascript" language="javascript" src="${webimroot}/js/compiled/common.js"></script>
	<script type="text/javascript" language="javascript" src="${webimroot}/js/compiled/leavemessage.js"></script>
	<script type="text/javascript">
${if:groups}
	    var groupDescriptions = ${page:group.descriptions};
${endif:groups}
	</script>
</head>
<body class="bgbody">

	<form name="leaveMessageForm" method="post" action="${webimroot}/leavemessage.php">
	<input type="hidden" name="style" value="${styleid}"/>
	<input type="hidden" name="info" value="${form:info}"/>
	<input type="hidden" name="referrer" value="${page:referrer}"/>
	${ifnot:groups}${if:formgroupid}<input type="hidden" name="group" value="${form:groupid}"/>${endif:formgroupid}${endif:groups}

	<div id="top2">
		<div id="logo">
			${if:company.chatLogoURL}
				${if:webimHost}
					<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
						<img src="${page:company.chatLogoURL}" alt=""/>
					</a>
				${else:webimHost}
					<img src="${page:company.chatLogoURL}" alt=""/>
				${endif:webimHost}
			${else:company.chatLogoURL}
				${if:webimHost}
					<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
						<img src="${tplroot}/images/default-logo.gif" alt=""/>
					</a>
				${else:webimHost}
					<img src="${tplroot}/images/default-logo.gif" alt=""/>
				${endif:webimHost}
			${endif:company.chatLogoURL}
			&nbsp;
			<div id="page-title">${if:formgroupname}${form:groupname}: ${endif:formgroupname}${msg:leavemessage.title}</div>
			<div class="clear">&nbsp;</div>
		</div>
	</div>
	<div id="headers">
		<div class="wndb"><div class="wndl"><div class="wndr"><div class="wndt"><div class="wndtl"><div class="wndtr"><div class="wndbl"><div class="wndbr">
			<div class="buttons">
				<a href="javascript:window.close();" title="${msg:leavemessage.close}"><img class="tplimage iclosewin" src="${webimroot}/images/free.gif" alt="${msg:leavemessage.close}" /></a>
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
		${if:groups}
			<tr>
				<td class="text">${msg:form.field.department}</td>
				<td>
				<select name="group" style="min-width:200px;" onchange="MessageForm.changeGroup(this, 'departmentDescription', groupDescriptions)">${page:groups}</select>
				</td>
			</tr>
			<tr>
				<td class="text">${msg:form.field.department.description}</td>
				<td class="text" id="departmentDescription">${page:default.department.description}</td>
			</tr>
		${endif:groups}
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
