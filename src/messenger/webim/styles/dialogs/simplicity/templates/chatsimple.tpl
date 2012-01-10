<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>${msg:chat.window.title.user}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css">
<script type="text/javascript" language="javascript" src="${webimroot}/js/${jsver}/brws.js"></script>
</head>
<body style="background:#EFEFEF;">
<div id="greybg">
	<table id="toolbar" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				${if:user}
					${if:canChangeName}
						<table cellpadding="0" cellspacing="5" border="0"><tr>
							<td class="text" nowrap>${msg:chat.client.name}</td>
							<td><input id="uname" type="text" size="12" value="${page:ct.user.name}" class="field"></td>
							<td><a href="javascript:void(0)" onClick="return false;" title="${msg:chat.client.changename}"><img src='${tplroot}/images/buttons/exec.gif' border="0" alt="${msg:chat.client.changename}" /></a></td>
						</tr></table>
					${else:canChangeName}
						<table cellpadding="0" cellspacing="5" border="0"><tr><td>
						${msg:chat.client.name}&nbsp;${page:ct.user.name}
						</td></tr></table>
					${endif:canChangeName}
				${endif:user}
			</td>
			<td align="right">
				<table cellpadding="0" cellspacing="5" border="0"><tr>
					<td>
						<a href="${page:mailLink}" target="_blank" title="${msg:chat.window.toolbar.mail_history}" onClick="this.newWindow = window.open('${page:mailLink}', 'ForwardMail', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=603,height=254,resizable=0');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src='${tplroot}/images/buttons/email.gif' border="0" alt="${msg:chat.window.toolbar.mail_history}" /></a>
					</td>
					<td>
						<a id="refresh" href="javascript:void(0)" onClick="return false;" title="${msg:chat.window.toolbar.refresh}"><img src='${tplroot}/images/buttons/refresh.gif' border="0" alt="${msg:chat.window.toolbar.refresh}" /></a>
					</td>
					<td>
						<a class="closethread" href="javascript:void(0)" onClick="return false;" title="${msg:chat.window.close_title}"><img src="${tplroot}/images/buttons/closewin.gif" border="0" alt="${msg:chat.window.close_title}"/></a>
					</td>
				</tr></table>
			</td>
		</tr>
	</table>
	<table id="chat" cellpadding="0" cellspacing="5" border="0" width="100%">
		<tr>
			<td>
				<iframe id="chatwnd" class="chathistory" name="chatwndiframe" src="${webimroot}/thread.php?act=refresh&amp;thread=${page:ct.chatThreadId}&amp;token=${page:ct.token}&amp;html=on&amp;user=true" frameborder="0">
					Sorry, your browser does not support iframes; try a browser that supports W3C standards.
				</iframe>
			</td>
			<td width="100" valign="top"></td>
		</tr>
		<tr>
			<td>
				<form id="messageform" method="post" action="${webimroot}/thread.php" target="chatwndiframe">
					<input type="hidden" name="act" value="post"/><input type="hidden" name="html" value="on"/><input type="hidden" name="thread" value="${page:ct.chatThreadId}"/><input type="hidden" name="token" value="${page:ct.token}"/><input type="hidden" name="user" value="true"/>
					<input type="hidden" id="message" name="message" value=""/>
					<textarea id="messagetext" cols="50" rows="4" class="message" style="width:520px;" tabindex="0"></textarea>
				</form>
			</td>
			<td width="100" valign="top">
				<table cellspacing="0" cellpadding="0" border="0" id="postmessage">
                	<tr>
                    	<td class="submit">
							<a id="msgsend1" href="javascript:void(0)" onClick="return false;" title="${msg:chat.window.send_message}"><img src="${tplroot}/images/buttons/send.jpg" border="0" alt="${msg:chat.window.send_message_short,send_shortcut}" /></a>
						</td>
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
<script type="text/javascript"><!--
function sendmessage(){
	getEl('message').value = getEl('messagetext').value;
	getEl('messagetext').value = '';
	getEl('messageform').submit();
}
getEl('messagetext').onkeydown = function(k) {
	if( k ){k=k.which; } else { k=event.keyCode; }
	if( (k==13) || (k==10) ) {
		sendmessage();
		return false;
	}
	return true;
}
getEl('msgsend1').onclick = function() {
	sendmessage();
	return false;
}
//--></script>
</body>
</html>
