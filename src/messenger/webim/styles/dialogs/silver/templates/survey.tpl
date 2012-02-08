<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>${msg:presurvey.title}</title>
	<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
	<script type="text/javascript" language="javascript" src="${webimroot}/js/${jsver}/common.js"></script>
	<script type="text/javascript" language="javascript" src="${webimroot}/js/${jsver}/survey.js"></script>
	<script type="text/javascript">
	${if:groups}
	    var groupDescriptions = ${page:group.descriptions};
	${endif:groups}
	    var localizedStrings = {
	${if:showemail}
		wrongEmail: '${msg:presurvey.error.wrong_email}',
	${endif:showemail}
	    }
	</script>
</head>
<body class="bgbody">
	<div id="top2">
		<div id="logo">
			${if:ct.company.chatLogoURL}
				${if:webimHost}
					<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
						<img src="${page:ct.company.chatLogoURL}" alt=""/>
					</a>
				${else:webimHost}
					<img src="${page:ct.company.chatLogoURL}" alt=""/>
				${endif:webimHost}
			${else:ct.company.chatLogoURL}
				${if:webimHost}
					<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
						<img src="${tplroot}/images/default-logo.gif" alt=""/>
					</a>
				${else:webimHost}
					<img src="${tplroot}/images/default-logo.gif" alt=""/>
				${endif:webimHost}
			${endif:ct.company.chatLogoURL}
			&nbsp;
			<div id="page-title">${msg:presurvey.title}</div>
			<div class="clear">&nbsp;</div>
		</div>
	</div>
	<div id="headers">
		<div class="wndb"><div class="wndl"><div class="wndr"><div class="wndt"><div class="wndtl"><div class="wndtr"><div class="wndbl"><div class="wndbr">
			<div class="buttons">
				<a href="javascript:window.close();" title="${msg:leavemessage.close}"><img class="tplimage iclosewin" src="${webimroot}/images/free.gif" alt="${msg:leavemessage.close}" /></a>
			</div>
			<div class="messagetxt">${msg:presurvey.intro}</div>
		</div></div></div></div></div></div></div></div>
	</div>
	<div id="content-wrapper">
	
		<form name="surveyForm" method="post" action="${webimroot}/client.php" />
		<input type="hidden" name="style" value="${styleid}"/>
		<input type="hidden" name="info" value="${form:info}"/>
		<input type="hidden" name="referrer" value="${page:referrer}"/>
		<input type="hidden" name="survey" value="on"/>
		${ifnot:showemail}<input type="hidden" name="email" value="${form:email}"/>${endif:showemail}
		${ifnot:groups}${if:formgroupid}<input type="hidden" name="group" value="${form:groupid}"/>${endif:formgroupid}${endif:groups}
		${ifnot:showmessage}<input type="hidden" name="message" value="${form:message}"/>${endif:showmessage}

		<table class="form">
		${if:groups}
			<tr>
				<td><strong>${msg:presurvey.department}</strong></td>
				<td><select name="group" onchange="Survey.changeGroup()">${page:groups}</select></td>
			</tr>
			<tr>
				<td><strong>${msg:presurvey.department.description}</strong></td>
				<td id="departmentDescription">${page:default.department.description}</td>
			</tr>
		${endif:groups}
			<tr>
				<td><strong>${msg:presurvey.name}</strong></td>
				<td><input type="text" name="name" size="50" value="${form:name}" class="username" ${ifnot:showname}disabled="disabled"${endif:showname}/></td>
			</tr>
		${if:showemail}
			<tr>
				<td><strong>${msg:presurvey.mail}</strong></td>
				<td><input type="text" name="email" size="50" value="${form:email}" class="username"/></td>
			</tr>
		${endif:showemail}
		${if:showmessage}			
			<tr>
				<td><strong>${msg:presurvey.question}:</strong></td>
				<td valign="top"><textarea name="message" tabindex="0" cols="45" rows="2">${form:message}</textarea></td>
			</tr>
		${endif:showmessage}			
		</table>		
		<a href="javascript:Survey.submit();" class="but" id="sndmessagelnk">${msg:presurvey.submit}</a>
		<div class="clear">&nbsp;</div>
	</div>
</body>
</html>