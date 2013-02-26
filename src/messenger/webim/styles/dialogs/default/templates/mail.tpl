<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>${msg:chat.window.title.user}</title>
	<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body>

	<form name="mailThreadForm" method="post" action="${webimroot}/mail.php"><input type="hidden" name="style" value="${styleid}"/>
	<input type="hidden" name="thread" value="${page:chat.thread.id}"/><input type="hidden" name="token" value="${page:chat.thread.token}"/><input type="hidden" name="level" value="${page:level}"/>

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
			&nbsp;<br />&nbsp;
			<div id="page-title">${msg:mailthread.title}</div>
			<div class="clear">&nbsp;</div>
		</div>
	</div>
	<div id="headers">
		<div class="wndb"><div class="wndl"><div class="wndr"><div class="wndt"><div class="wndtl"><div class="wndtr"><div class="wndbl"><div class="wndbr">
			<div class="buttons">
				<a href="javascript:window.close();" title="${msg:mailthread.close}"><img class="tpl-image iclosewin" src="${webimroot}/images/free.gif" alt="${msg:mailthread.close}" /></a>
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
