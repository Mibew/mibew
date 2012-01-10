<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:leavemessage.title}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body>
<div id="whitebg">
	<form name="leaveMessageForm" method="post" action="${webimroot}/leavemessage.php">
		<input type="hidden" name="style" value="${styleid}"/>
		<input type="hidden" name="info" value="${form:info}"/>
		<input type="hidden" name="referrer" value="${page:referrer}"/>
		${if:formgroupid}<input type="hidden" name="group" value="${form:groupid}"/>${endif:formgroupid}
		<table cellpadding="0" cellspacing="5" border="0" width="100%">
			<tr>
				<td colspan="2">
					<h1>${if:formgroupname}${form:groupname}: ${endif:formgroupname}${msg:leavemessage.title}</h1>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table id="form" cellpadding="0" cellspacing="5" border="0">
						<tr>
							<td colspan="2">
								${msg:leavemessage.descr}
							</td>
						</tr>
						${if:errors}
							<tr>
								<td colspan="2">
									<table cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td valign="top"><img id="errorimage" src="${tplroot}/images/error.gif" border="0" alt=""/></td>
											<td>${errors}</td>
										</tr>
									</table>
								</td>
							</tr>
						${endif:errors}
						<tr>
							<td class="text">${msg:form.field.email}:</td>
							<td><input type="text" name="email" size="50" value="${form:email}" class="field"/></td>
						</tr>
						<tr>
							<td class="text">${msg:form.field.name}:</td>
							<td><input type="text" name="name" size="50" value="${form:name}" class="field"/></td>
						</tr>
						<tr>
							<td class="text" valign="top">${msg:form.field.message}:</td>
							<td><textarea name="message" cols="45" rows="8"  class="field" tabindex="0">${form:message}</textarea></td>
						</tr>
${if:showcaptcha}
						<tr>
							<td class="text"><img src="captcha.php"/></td>
							<td><input type="text" name="captcha" size="50" maxlength="15" value="" class="username"/></td>
						</tr>
${endif:showcaptcha}
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table cellspacing="3" cellpadding="0" border="0">
						<tr>
							<td><a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}"><img src="${tplroot}/images/buttons/exec.gif" border="0" alt="${msg:leavemessage.perform}"/></a></td>
							<td class="button"><a href="javascript:document.leaveMessageForm.submit();" title="${msg:leavemessage.perform}">${msg:leavemessage.perform}</a></td>
						</tr>
					</table>
				</td>
				<td align="right">
					<table cellspacing="3" cellpadding="0" border="0">
						<tr>
							<td><a href="javascript:window.close();" title="${msg:page.chat.old_browser.close}"><img src="${tplroot}/images/buttons/closewin.gif" border="0" alt="${msg:page.chat.old_browser.close}"/></a></td>
							<td class="button"><a href="javascript:window.close();" title="${msg:page.chat.old_browser.close}">${msg:page.chat.old_browser.close}</a></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<table id="footer" cellpadding="0" cellspacing="5" border="0" width="100%">
		<tr>
			<td valign="top">
				${msg:chat.window.poweredby} <a id="poweredByLink" href="http://mibew.org" title="Mibew Community" target="_blank">mibew.org</a>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
