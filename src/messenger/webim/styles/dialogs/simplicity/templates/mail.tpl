<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:chat.window.title.user}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body>
<div id="whitebg">
	<form name="mailThreadForm" method="post" action="${webimroot}/mail.php">
		<input type="hidden" name="style" value="${styleid}"/>
		<input type="hidden" name="thread" value="${page:ct.chatThreadId}"/>
		<input type="hidden" name="token" value="${page:ct.token}"/>
		<input type="hidden" name="level" value="${page:level}"/>
		<table cellpadding="0" cellspacing="5" border="0" width="100%">
			<tr>
				<td colspan="2">
					<h1>${msg:mailthread.title}</h1>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table id="form" cellpadding="0" cellspacing="5" border="0">
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
							<td class="text">${msg:mailthread.enter_email}</td>
							<td><input type="text" name="email" size="20" value="${form:email}" class="field"/></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table cellspacing="3" cellpadding="0" border="0">
						<tr>
							<td><a href="javascript:document.mailThreadForm.submit();" title="${msg:mailthread.perform}"><img src="${tplroot}/images/buttons/exec.gif" border="0" alt="${msg:mailthread.perform}"/></a></td>
							<td class="button"><a href="javascript:document.mailThreadForm.submit();" title="${msg:leavemessage.perform}">${msg:mailthread.perform}</a></td>
						</tr>
					</table>
				</td>
				<td align="right">
					<table cellspacing="3" cellpadding="0" border="0">
						<tr>
							<td><a href="javascript:window.close();" title="${msg:mailthread.close}"><img src="${tplroot}/images/buttons/closewin.gif" border="0" alt="${msg:mailthread.close}"/></a></td>
							<td class="button"><a href="javascript:window.close();" title="${msg:mailthread.close}">${msg:mailthread.close}</a></td>
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

