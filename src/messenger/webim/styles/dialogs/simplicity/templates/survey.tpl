<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:presurvey.title}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body>
<div id="whitebg">
  	<form name="surveyForm" method="post" action="${webimroot}/client.php">
		<input type="hidden" name="style" value="${styleid}"/>
		<input type="hidden" name="info" value="${form:info}"/>
		<input type="hidden" name="referrer" value="${page:referrer}"/>
		<input type="hidden" name="survey" value="on"/>
		${ifnot:showemail}<input type="hidden" name="email" value="${form:email}"/>${endif:showemail}
		${ifnot:groups}${if:formgroupid}<input type="hidden" name="group" value="${form:groupid}"/>${endif:formgroupid}${endif:groups}
		${ifnot:showmessage}<input type="hidden" name="message" value="${form:message}"/>${endif:showmessage}
		<table cellpadding="0" cellspacing="5" border="0" width="100%">
			<tr>
				<td colspan="2">
					<h1>${msg:presurvey.title}</h1>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table id="form" cellpadding="0" cellspacing="5" border="0">
						<tr>
							<td colspan="2">
								${msg:presurvey.intro}
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
						${if:groups}
							<tr>
								<td class="text">${msg:presurvey.department}</td>
								<td><select name="group" style="min-width:200px;">${page:groups}</select></td>
							</tr>
						${endif:groups}
						<tr>
							<td class="text">${msg:presurvey.name}</td>
							<td><input type="text" name="name" size="50" value="${form:name}" class="field" ${ifnot:showname}disabled="disabled"${endif:showname}/></td>
						</tr>
						${if:showemail}
							<tr>
								<td class="text">${msg:presurvey.mail}</td>
								<td><input type="text" name="email" size="50" value="${form:email}" class="field"/></td>
							</tr>
						${endif:showemail}
						${if:showmessage}
							<tr>
								<td class="text">${msg:presurvey.question}</td>
								<td><textarea name="message" class="field" tabindex="0" cols="45" rows="2" style="overflow:auto">${form:message}</textarea></td>
							</tr>
						${endif:showmessage}
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table cellspacing="3" cellpadding="0" border="0">
						<tr>
							<td><a href="javascript:document.surveyForm.submit();" title="${msg:presurvey.submit}"><img src="${tplroot}/images/buttons/exec.gif" border="0" alt="${msg:presurvey.submit}"/></a></td>
							<td class="button"><a href="javascript:document.surveyForm.submit();" title="${msg:presurvey.submit}">${msg:presurvey.submit}</a></td>
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
