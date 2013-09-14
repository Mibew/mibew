<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>${msg:chat.window.title.agent}</title>
<link rel="shortcut icon" href="${mibewroot}/images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css">
<script type="text/javascript" language="javascript" src="${mibewroot}/js/${jsver}/common.js"></script>
<script type="text/javascript" language="javascript" src="${mibewroot}/js/${jsver}/brws.js"></script>
<script type="text/javascript" language="javascript"><!--
var threadParams = { servl:"${mibewroot}/thread.php",wroot:"${mibewroot}",frequency:${page:frequency},${if:user}user:"true",${endif:user}threadid:${page:ct.chatThreadId},token:${page:ct.token},cssfile:"${tplroot}/chat.css",ignorectrl:${page:ignorectrl} };
//-->
</script>
<script type="text/javascript" language="javascript" src="${mibewroot}/js/${jsver}/chat.js"></script>
<style type="text/css">
.isound { background: url(${tplroot}/images/buttons/sound.gif) no-repeat; width: 19px; height: 19px; }
.inosound { background: url(${tplroot}/images/buttons/nosound.gif) no-repeat; width: 19px; height: 19px; }
</style>
</head>
<body style="background:#EFEFEF;">
<div id="greybg">
	<table id="toolbar" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				${if:agent}
					<table cellpadding="0" cellspacing="5" border="0"><tr>
						<td>
							${if:historyParams}
								${msg:chat.window.chatting_with}
								<a href="${page:historyParamsLink}" target="_blank" title="${msg:page.analysis.userhistory.title}" onClick="this.newWindow = window.open('${page:historyParamsLink}', 'UserHistory', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=703,height=380,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;">${page:ct.user.name}</a>
							${else:historyParams}
								${msg:chat.window.chatting_with} <b>${page:ct.user.name}</b>
							${endif:historyParams}
						</td>
						<td>
							<a class="closethread" href="javascript:void(0)" onClick="return false;" title="${msg:chat.window.close_title}"><img src="${tplroot}/images/buttons/close.gif" border="0" alt="${msg:chat.window.close_title}"/></a>
						</td>
					</tr></table>
				${endif:agent}
				${if:user}
					${if:canChangeName}
						<div id="changename1" style="display:${page:displ1};">
							<table cellpadding="0" cellspacing="5" border="0">
								<tr>
									<td class="text" nowrap="nowrap">${msg:chat.client.name}</td>
									<td><input id="uname" type="text" size="12" value="${page:ct.user.name}" class="field"></td>
									<td><a href="javascript:void(0)" onClick="return false;" title="${msg:chat.client.changename}"><img src="${tplroot}/images/buttons/exec.gif" border="0" alt="${msg:chat.client.changename}" /></a></td>
								</tr>
							</table>
						</div>
						<div id="changename2" style="display:${page:displ2};">
							<table cellpadding="0" cellspacing="5" border="0">
								<tr>
									<td class="text" nowrap="nowrap"><a id="unamelink" href="javascript:void(0)" onClick="return false;" title="${msg:chat.client.changename}">${page:ct.user.name}</a></td>
									<td><a href="javascript:void(0)" onClick="return false;" title="${msg:chat.client.changename}"><img src="${tplroot}/images/buttons/change.gif" border="0" alt="${msg:chat.client.changename}" /></a></td>
								</tr>
							</table>
						</div>
					${else:canChangeName}
						<table cellpadding="0" cellspacing="5" border="0"><tr><td>
						${msg:chat.client.name}&nbsp;${page:ct.user.name}
						</td></tr></table>
					${endif:canChangeName}
				${endif:user}
			</td>
			<td align="right">
				<table cellpadding="0" cellspacing="5" border="0"><tr>
				${if:user}
					<td>
						<a href="${page:mailLink}&amp;style=${styleid}" target="_blank" title="${msg:chat.window.toolbar.mail_history}" onClick="this.newWindow = window.open('${page:mailLink}&amp;style=${styleid}', 'ForwardMail', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=603,height=254,resizable=0'); if (this.newWindow != null) {this.newWindow.focus();this.newWindow.opener=window;}return false;"><img src="${tplroot}/images/buttons/email.gif" border="0" alt="${msg:chat.window.toolbar.mail_history}"/></a>
					</td>
				${endif:user}
				${if:agent}
					${if:canpost}
						<td>
							<a href="${page:redirectLink}&amp;style=${styleid}" title="${msg:chat.window.toolbar.redirect_user}"><img src="${tplroot}/images/buttons/redirect.gif" border="0" alt="${msg:chat.window.toolbar.redirect_user}" /></a>
						</td>
					${endif:canpost}
					${if:historyParams}
						<td>
							<a href="${page:historyParamsLink}" target="_blank" title="${msg:page.analysis.userhistory.title}" onClick="this.newWindow = window.open('${page:historyParamsLink}', 'UserHistory', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=720,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="${tplroot}/images/buttons/history.gif" border="0" alt="${msg:page.analysis.userhistory.title}"/></a>
						</td>
					${endif:historyParams}
				${endif:agent}
				<td>
					<a id="togglesound" href="javascript:void(0)" onClick="return false;" title="${msg:chat.window.toolbar.toggle_sound}"><img id="soundimg" class="isound" src="${mibewroot}/images/free.gif" border="0" alt="Sound On/Off" /></a>
				</td>
				<td>
					<a id="refresh" href="javascript:void(0)" onClick="return false;" title="${msg:chat.window.toolbar.refresh}"><img src="${tplroot}/images/buttons/refresh.gif" border="0" alt="${msg:chat.window.toolbar.refresh}" /></a>
				</td>
				${if:sslLink}
					<td>
						<a href="${page:sslLink}&amp;style=${styleid}" title="SSL" ><img src="${tplroot}/images/buttons/ssl.gif" border="0" alt="SSL"/></a>
					</td>
				${endif:sslLink}
				<td>
					<a class="closethread" href="javascript:void(0)" onClick="return false;" title="${msg:chat.window.close_title}"><img src="${tplroot}/images/buttons/close.gif" border="0" alt="${msg:chat.window.close_title}"/></a>
				</td>
				</tr></table>
			</td>
		</tr>
	</table>
	<table id="chat" cellpadding="0" cellspacing="5" border="0" width="100%">
		<tr>
			<td>
				<div id="engineinfo" style="display:none;"></div>
				<div id="typingdiv" style="display:none;">${msg:typing.remote}</div>&nbsp;
			</td>
			<td width="100"></td>
		</tr>
		<tr>
			<td valign="top">
				<iframe id="chatwnd" class="chathistory" src="${if:neediframesrc}${mibewroot}/images/blank.html${endif:neediframesrc}" frameborder="0">
					Sorry, your browser does not support iframes; try a browser that supports W3C standards.
				</iframe>
			</td>
			<td width="100" valign="top">
				<div id="avatarwnd"></div>
			</td>
		</tr>
		${if:canpost}
		<tr>
			<td valign="top">
				<textarea id="msgwnd" class="message" tabindex="0"></textarea>
			</td>
			<td width="100" valign="top">
				<table cellspacing="0" cellpadding="0" border="0" id="postmessage">
               		<tr>
                    	<td class="submit">
                			<a id="sndmessagelnk" href="javascript:void(0)" onClick="return false;" title="${msg:chat.window.send_message}"><img src="${tplroot}/images/buttons/send.jpg" border="0" alt="${msg:chat.window.send_message_short,send_shortcut}" /></a>
						</td>
                	</tr>
            	</table>
			</td>
		</tr>
		<tr>
			<td>
				${if:agent}
					<select id="predefined" size="1" class="dropdown">
						<option>${msg:chat.window.predefined.select_answer}</option>
						${page:predefinedAnswers}
					</select>
				${endif:agent}
			</td>
			<td></td>
		</tr>
		${endif:canpost}
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

