<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>${msg:chat.window.title.agent}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<meta http-equiv="Content-Type" content="text/html; charset=Windows-1251">
<link rel="stylesheet" type="text/css" href="${webimroot}/chat.css" />
<script type="text/javascript" language="javascript" src="${webimroot}/js/common.js"></script>
<script type="text/javascript" language="javascript" src="${webimroot}/js/brws.js"></script>
<script language="javascript"><!--
var threadParams = { servl:"${webimroot}/thread.php",wroot:"${webimroot}",frequency:2,${if:user}user:"true",${endif:user}threadid:${page:ct.chatThreadId},token:${page:ct.token} };
//--></script>
<script type="text/javascript" language="javascript" src="${webimroot}/js/chat.js"></script>
</head>

<body bgcolor="#FFFFFF" background="${webimroot}/images/bg.gif" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" marginwidth="0" marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">

<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

	<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td></td>
    <td colspan="2" height="100" background="${webimroot}/images/banner.gif" valign="top" class="bgrn">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="50%" valign="top">
			<table width="135" cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td height="10"></td>
			</tr>
			<tr>
		    <td align="center">
		    	${if:ct.company.chatLogoURL}
		    		${if:webimHost}
		            	<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
			            	<img src="${page:ct.company.chatLogoURL}" border="0" alt="">
			            </a>
			        ${else:webimHost}
		            	<img src="${page:ct.company.chatLogoURL}" border="0" alt="">
			        ${endif:webimHost}
		        ${endif:ct.company.chatLogoURL}
		    </td>
			</tr>
			<tr>
		    <td height="5"></td>
			</tr>
            ${ifnot:ct.company.chatLogoURL}
			<tr>
		    <td align="center" class="text">
	    		${if:webimHost}
	            	<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">${page:ct.company.name}</a>
			    ${else:webimHost}
			    	${page:ct.company.name}
			    ${endif:webimHost}
			</td>
			</tr>
            ${endif:ct.company.chatLogoURL}
			</table>
		</td>
    	<td width="50%" align="right" valign="top">
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
			    <td><a class="closethread" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.close_title}"><img src='${webimroot}/images/buttons/closewin.gif' width="15" height="15" border="0" altKey="chat.window.close_link_text"/></a></td>
			    <td width="5"></td>
				</tr>
				</table>

			</td>
			</tr>

			<tr>
		    <td height="60" align="right">

				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
${if:agent}
				<td class="text" nowrap>
            ${if:historyParams}
				${msg:chat.window.chatting_with}
				<a href="${page:historyParamsLink}" target="_blank" title="${msg:page.analysis.userhistory.title}" onclick="this.newWindow = window.open('${page:historyParamsLink}', 'UserHistory', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=703,height=380,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;">${print:ct.user.name}</a>
			${else:historyParams}
				${msg:chat.window.chatting_with} <b>${print:ct.user.name}</b>
			${endif:historyParams}
				</td>
${endif:agent}
${if:user}
	${if:canChangeName}
				<td class="text" nowrap>
				<div id="changename1" style="display:${page:displ1};">
					<table cellspacing="0" cellpadding="0" border="0"><tr>
					<td class="text" nowrap>${msg:chat.client.name}</td>
					<td width="10" valign="top"><img src='${webimroot}/images/free.gif' width="10" height="1" border="0" alt="" /></td>
					<td><input id="uname" type="text" size="12" value="${page:ct.user.name}" class="username"></td>
					<td width="5" valign="top"><img src='${webimroot}/images/free.gif' width="5" height="1" border="0" alt="" /></td>
					<td><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}"><img src='${webimroot}/images/buttons/exec.gif' width="25" height="25" border="0" alt="&gt;&gt;" /></a></td>
					</tr></table>
				</div>
				<div id="changename2" style="display:${page:displ2};">
					<table cellspacing="0" cellpadding="0" border="0"><tr>
					<td class="text" nowrap><a id="unamelink" href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}">${page:ct.user.name}</a></td>
					<td width="10" valign="top"><img src='${webimroot}/images/free.gif' width="10" height="1" border="0" alt="" /></td>
					<td><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}"><img src='${webimroot}/images/buttons/changeuser.gif' width="25" height="25" border="0" alt="" /></a></td>
					</tr></table>
				</div>
				</td>
	${else:canChangeName}
				<td class="text" nowrap>
				${msg:chat.client.name}&nbsp;${page:ct.user.name}
				</td>
	${endif:canChangeName}
${endif:user}
${if:agent}
				<td width="10" valign="top"><img src='${webimroot}/images/free.gif' width="10" height="1" border="0" alt="" /></td>
				<td><a class="closethread" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.close_title}">
				<img src='${webimroot}/images/buttons/close.gif' width="25" height="25" border="0" altKey="chat.window.close_link_text"/></a></td>
${endif:agent}

			    <td><img src='${webimroot}/images/buttondiv.gif' width="35" height="45" border="0" alt="" /></td>
${if:user}
				<td><a href="${page:selfLink}&act=mailthread" target="_blank" title="${msg:chat.window.toolbar.mail_history}" onclick="this.newWindow = window.open('${page:selfLink}&act=mailthread', 'ForwardMail', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=603,height=254,resizable=0');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src='${webimroot}/images/buttons/email.gif' width="25" height="25" border="0" alt="Mail&nbsp;"/></a></td>
${endif:user}
${if:agent}
${if:canpost}
				<td><a href="${page:selfLink}&act=redirect" title="${msg:chat.window.toolbar.redirect_user}">
				<img src='${webimroot}/images/buttons/send.gif' width="25" height="25" border="0" alt="Redirect&nbsp;" /></a></td>
${endif:canpost}
${if:historyParams}
				<td><a href="${page:historyParamsLink}" target="_blank" title="${msg:page.analysis.userhistory.title}" onclick="this.newWindow = window.open('${page:historyParamsLink}', 'UserHistory', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=703,height=380,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src='${webimroot}/images/buttons/history.gif' width="25" height="25" border="0" alt="History&nbsp;"/></a></td>
${endif:historyParams}
${endif:agent}
				<td><a id="refresh" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.toolbar.refresh}">
				<img src='${webimroot}/images/buttons/refresh.gif' width="25" height="25" border="0" alt="Refresh&nbsp;" /></a></td>

				<td width="20" valign="top"><img src='${webimroot}/images/free.gif' width="20" height="1" border="0" alt="" /></td>
				</tr>
				</table>

			</td>
			</tr>

			<tr>
		    <td height="15" align="right">
		    	<div id="engineinfo" style="display:none;">
		    	</div>
				<div id="typingdiv" style="display:none;">
					${msg:typing.remote}
				</div>
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

		<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="20" valign="top"><img src='${webimroot}${url:image.chat.history}' width="20" height="80" border="0" alt="" /></td>
    	<td colspan="2" width="100%" height="100%" valign="top" id="chatwndtd">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td bgcolor="#A1A1A1"><img src='${webimroot}/images/free.gif' width="1" height="1" border="0" alt="" /></td>
		    <td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
				<iframe id="chatwnd" width="100%" height="100%" src="${if:neediframesrc}${webimroot}/images/blank.html${endif:neediframesrc}" frameborder="0" style="overflow:auto;">
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
${if:canpost}
		<tr>
	    <td colspan="3" height="5" style="cursor:n-resize;" id="spl1"></td>
		</tr>

		<tr>
	    <td width="20" valign="top"><img src='${webimroot}${url:image.chat.message}' width="20" height="85" border="0" alt="" /></td>
	    ${if:isOpera95}
    	<td width="100%" height="60%" valign="top" id="msgwndtd">
    	${else:isOpera95}
    	<td width="100%" height="100" valign="top" id="msgwndtd">
    	${endif:isOpera95}
			<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0"><tr><td colspan="3" bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
				<textarea id="msgwnd" class="message" tabindex="0"></textarea>
			</td><td bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td colspan="3" bgcolor="#A1A1A1"><img src="${webimroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr></table>
		</td>
	    <td valign="center" id="avatarwnd"></td>
		</tr>
${endif:canpost}
		</table>

	</td>
    <td></td>
	</tr>

	<tr>
    <td height="45"></td>
    <td>
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td align="left">
			<table cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td width="20"></td>

${if:agent}${if:canpost}
		    <td>
				<select id="predefined" size="1" class="answer">
				<option>${msg:chat.window.predefined.select_answer}</option>
				${page:predefinedAnswers}
				</select>
			</td>
${endif:canpost}${endif:agent}
			</tr>
			</table>
		</td>
		<td align="center" class="copyr">${msg:chat.window.poweredby} <a href="${msg:site.url}" title="${msg:company.title}" target="_blank">${msg:chat.window.poweredreftext}</a></td>
		<td align="right">

${if:canpost}
			<table cellspacing="0" cellpadding="0" border="0" id="postmessage">

			<tr>

		    <td><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}"><img src='${webimroot}/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
		    <td background="${webimroot}/images/submitbg.gif" valign="top" class="submit">
				<img src='${webimroot}/images/free.gif' width="1" height="10" border="0" alt="" /><br>
				<a id="sndmessagelnk" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}">${msg:chat.window.send_message_short,send_shortcut}</a><br>
			</td>
			<td width="10"><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}"><img src='${webimroot}/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
			</tr>
			</table>
${endif:canpost}
		</td>
		</tr>
		</table>
	</td>
    <td></td>
	</tr>

	<tr>
    <td width="10"><img src='${webimroot}/images/free.gif' width="10" height="1" border="0" alt="" /></td>
    <td width="100%"><img src='${webimroot}/images/free.gif' width="585" height="1" border="0" alt="" /></td>
    <td width="5"><img src='${webimroot}/images/free.gif' width="5" height="1" border="0" alt="" /></td>
	</tr>
	</table>

</td>
</tr>
</table>

</body>
</html>

