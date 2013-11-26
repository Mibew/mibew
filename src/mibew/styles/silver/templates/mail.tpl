<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>${msg:chat.window.title.user}</title>
	<link rel="shortcut icon" href="${mibewroot}/images/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body class="bgbody">

	<form name="mailThreadForm" method="post" action="${mibewroot}/mail.php"><input type="hidden" name="style" value="${styleid}"/>
	<input type="hidden" name="thread" value="${page:ct.chatThreadId}"/><input type="hidden" name="token" value="${page:ct.token}"/><input type="hidden" name="level" value="${page:level}"/>

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
			&nbsp;<br />&nbsp;
			<div id="page-title">${msg:mailthread.title}</div>
			<div class="clear">&nbsp;</div>
		</div>
	</div>
	<div id="headers">
		<div class="wndb"><div class="wndl"><div class="wndr"><div class="wndt"><div class="wndtl"><div class="wndtr"><div class="wndbl"><div class="wndbr">
			<div class="buttons">
				<a href="javascript:window.close();" title="${msg:mailthread.close}"><img class="tplimage iclosewin" src="${mibewroot}/images/free.gif" alt="${msg:mailthread.close}" /></a>
			</div>
			<div class="messagetxt">
				<strong>${msg:mailthread.enter_email}</strong>
				<input type="text" name="email" size="20" value="${form:email}" class="username" />&nbsp;
				<a href="javascript:document.mailThreadForm.submit();">${msg:mailthread.perform}</a>
			</div>
		</div></div></div></div></div></div></div></div>
	</div>
	<div id="content-wrapper">
		${if:errors}
			${errors}
		${endif:errors}
	</div>
	</form>
</body>
</html>
