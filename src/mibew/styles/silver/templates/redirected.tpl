<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>${msg:chat.window.title.user}</title>
	<link rel="shortcut icon" href="${mibewroot}/images/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body class="bgbody">
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
			<div id="page-title">${msg:chat.redirected.title}</div>
			<div class="clear">&nbsp;</div>
		</div>
	</div>
	<div id="headers">
		<div class="wndb"><div class="wndl"><div class="wndr"><div class="wndt"><div class="wndtl"><div class="wndtr"><div class="wndbl"><div class="wndbr">
			<div class="buttons">
				<a href="javascript:window.close();" title="${msg:chat.redirected.close}"><img class="tplimage iclosewin" src="${mibewroot}/images/free.gif" alt="${msg:chat.redirected.close}" /></a>
			</div>
			<div class="messagetxt">${page:message}</div>
		</div></div></div></div></div></div></div></div>
	</div>
	<div id="content-wrapper">
		&nbsp;
	</div>
</body>
</html>