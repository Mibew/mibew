<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>${msg:leavemessage.title}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body>
<div id="whitebg">
	<form name="leaveMessageForm" method="post" action="${webimroot}/leavemessage.php">
		<input type="hidden" name="style" value="${styleid}"/>
		<input type="hidden" name="info" value="${page:info}"/>
		<table cellpadding="0" cellspacing="5" border="0" width="100%">
			<tr>
				<td colspan="2">
					<h1>${msg:leavemessage.title}</h1>
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
							<td><textarea name="message" class="message" tabindex="0">${form:message}</textarea></td>
						</tr>
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
	<table id="footer" cellpadding="0" cellspacing="5" border="0" width="100%" height="100%">
		<tr>
			<td valign="top">
				${msg:chat.window.poweredby} <a href="${msg:site.url}" title="${msg:company.title}" target="_blank">${msg:chat.window.poweredreftext}</a>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
