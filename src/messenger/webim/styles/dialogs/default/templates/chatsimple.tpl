<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>${msg:chat.window.title.user}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css">
<script type="text/javascript" language="javascript" src="${webimroot}/js/${jsver}/brws.js"></script>
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400">

<table width="600" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

	<table width="600" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td></td>
    <td colspan="2" height="100" valign="top" class="bgrn">
		<table width="590" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="135" valign="top">
			<table width="135" cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td height="25"></td>
			</tr>
			<tr>
		    <td align="center">${if:ct.company.chatLogoURL}<img src="${page:ct.company.chatLogoURL}" width="85" height="45" border="0" alt="${page:ct.company.name}">${endif:ct.company.chatLogoURL}</td>
			</tr>
			<tr>
		    <td height="5"></td>
			</tr>
			<tr>
		    <td align="center" class="text">${page:ct.company.name}</td>
			</tr>
			</table>
		</td>
    	<td width="455" align="right" valign="top">
			<table cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td height="25" align="right">

				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
			    <td class="text">${msg:chat.window.product_name}</td>
			    <td width="5"></td>
			    <td>
					<table cellspacing="0" cellpadding="0" border="0">
					<tr>
				    <td width="95" height="13" bgcolor="#D09221" align="center" class="www"><a href="${msg:site.url}" title="${msg:company.title}" target="_blank">${msg:site.title}</a></td>
					</tr>
					</table>
				</td>
			    <td width="5"></td>
			    <td><a class="closethread" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.close_title}"><img src='${tplroot}/images/buttons/closewin.gif' width="15" height="15" border="0" alt="${msg:chat.window.close_title}"/></a></td>
			    <td width="5"></td>
				</tr>
				</table>

			</td>
			</tr>

			<tr>
		    <td height="60" align="right">

				<table cellspacing="0" cellpadding="0" border="0">
				<tr>

				<td class="text" nowrap>${msg:chat.client.name}</td>
				<td width="10" valign="top"><img src='${webimroot}/images/free.gif' width="10" height="1" border="0" alt="" /></td>
				<td><input id="uname" type="text" size="12" value="${page:ct.user.name}" class="username"></td>
				<td width="5" valign="top"><img src='${webimroot}/images/free.gif' width="5" height="1" border="0" alt="" /></td>
				<td><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}"><img src='${tplroot}/images/buttons/exec.gif' width="25" height="25" border="0" alt="&gt;&gt;" /></a></td>

			    <td><img src='${tplroot}/images/buttondiv.gif' width="35" height="45" border="0" alt="" /></td>

				<td><a href="${page:mailLink}" target="_blank" title="${msg:chat.window.toolbar.mail_history}" onclick="this.newWindow = window.open('${page:mailLink}', 'ForwardMail', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=603,height=254,resizable=0'); if (this.newWindow != null) {this.newWindow.focus();this.newWindow.opener=window;}return false;"><img src='${tplroot}/images/buttons/email.gif' width="25" height="25" border="0" alt="Mail" /></a></td>

				<td><a id="refresh" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.toolbar.refresh}">
				<img src='${tplroot}/images/buttons/refresh.gif' width="25" height="25" border="0" alt="Refresh" /></a></td>



			    <td width="20"></td>
				</tr>
				</table>

			</td>
			</tr>
			</table>
		</td>
		</tr>
		</table>
	</td>
	</tr>

	<tr>
    <td></td>
    <td valign="top">

		<table width="585" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="20" valign="top"><img src='${webimroot}${url:image.chat.history}' width="20" height="80" border="0" alt="History" /></td>
    	<td width="565" valign="top" id="chatwndtd">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
		    <td width="100%" bgcolor="#FFFFFF" valign="top">
				<iframe name="chatwndiframe" width="100%" height="175" src="${webimroot}/thread.php?act=refresh&amp;thread=${page:ct.chatThreadId}&amp;token=${page:ct.token}&amp;html=on&amp;user=true" frameborder="0" style="overflow:auto;">
				Sorry, your browser does not support iframes; try a browser that supports W3 standards.
				</iframe>
			</td>
		    <td bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			</table>
		</td>
		</tr>

		<tr>
	    <td colspan="2" height="5"></td>
		</tr>

		<tr>
	    <td width="20" valign="top"><img src='${webimroot}${url:image.chat.message}' width="20" height="85" border="0" alt="Message" /></td>
    	<td width="565" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
		    <td width="565" height="85" bgcolor="#FFFFFF" valign="top">
				<form id="messageform" method="post" action="${webimroot}/thread.php" target="chatwndiframe">
				<input type="hidden" name="act" value="post"/><input type="hidden" name="html" value="on"/><input type="hidden" name="thread" value="${page:ct.chatThreadId}"/><input type="hidden" name="token" value="${page:ct.token}"/><input type="hidden" name="user" value="true"/>
				<input type="hidden" id="message" name="message" value=""/>
				<textarea id="messagetext" cols="50" rows="4" class="message" style="width:550px;" tabindex="0"></textarea>
				</form>
			</td>
		    <td bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			</table>
		</td>
		</tr>
		</table>

	</td>
    <td></td>
	</tr>

	<tr>
    <td height="45"></td>
    <td>
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="33%">
			<table cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td width="20"></td>

			</tr>
			</table>
		</td>
		<td width="33%" align="center" class="copyr">${msg:chat.window.poweredby} <a id="poweredByLink" href="http://mibew.org" title="Mibew Community" target="_blank">mibew.org</a></td>
		<td width="33%" align="right">
			<table cellspacing="0" cellpadding="0" border="0" id="postmessage">

			<tr>
		    <td><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}"><img src='${webimroot}/images/submit.gif' width="40" height="35" border="0" alt=""/></a></td>
		    <td style="background-image: url(${webimroot}/images/submitbg.gif)" valign="top" class="submit">
				<img src='${webimroot}/images/free.gif' width="1" height="10" border="0" alt="" /><br/>
				<a id="msgsend1" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}">${msg:chat.window.send_message_short,send_shortcut}</a><br/>
			</td>
			<td width="10"><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}"><img src='${webimroot}/images/submitrest.gif' width="10" height="35" border="0" alt=""/></a></td>
			</tr>
			</table>
		</td>
		</tr>
		</table>
	</td>
    <td></td>
	</tr>

	<tr>
    <td width="10"><img src='${webimroot}/images/free.gif' width="10" height="1" border="0" alt="" /></td>
    <td width="585"><img src='${webimroot}/images/free.gif' width="585" height="1" border="0" alt="" /></td>
    <td width="5"><img src='${webimroot}/images/free.gif' width="5" height="1" border="0" alt="" /></td>
	</tr>
	</table>

</td>
</tr>
</table>

<script type="text/javascript"><!--
function sendmessage(){
	getEl('message').value = getEl('messagetext').value;
	getEl('messagetext').value = '';
	getEl('messageform').submit();
}
getEl('messagetext').onkeydown = function(k) {
	if( k ){ ctrl=k.ctrlKey;k=k.which; } else { k=event.keyCode;ctrl=event.ctrlKey;	}
	if( (k==13 && ctrl) || (k==10) ) {
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
