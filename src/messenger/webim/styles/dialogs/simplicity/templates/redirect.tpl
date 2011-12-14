<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:chat.window.title.agent}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
</head>
<body>
<div id="whitebg">
	<table cellpadding="0" cellspacing="5" border="0" width="100%">
		<tr>
			<td colspan="2">
				<h1>${msg:chat.redirect.title}</h1>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h2>${msg:chat.redirect.choose}</h2>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table id="form" cellspacing="3" cellpadding="0" border="0">
					<tr>
						<td>
							${if:redirectToAgent}
								${msg:chat.redirect.operator}
								<ul class="agentlist">
									${page:redirectToAgent}
								</ul>
							${endif:redirectToAgent}
							${if:redirectToGroup}
								${msg:chat.redirect.group}<br/>
								<ul class="agentlist">
									${page:redirectToGroup}
								</ul>
							${endif:redirectToGroup}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				${pagination}
			</td>
			<td align="right">
				<table cellspacing="3" cellpadding="0" border="0">
					<tr>
						<td><a href="javascript:history.back();" title="${msg:chat.redirect.back}"><img src="${tplroot}/images/buttons/back.gif" border="0" alt="${msg:chat.redirect.back}"/></a></td>
						<td class="button"><a href="javascript:history.back();" title="${msg:chat.redirect.back}">${msg:chat.redirect.back}</a></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
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

