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
.tplimage {
	background: transparent url(${tplroot}/images/wmchat.png) no-repeat scroll 0px 0px;
	width: 25px; height: 25px;
	-moz-background-clip: -moz-initial; 
	-moz-background-origin: -moz-initial; 
	-moz-background-inline-policy: -moz-initial;
}
.irefresh { background-position:0px -24px; }
.iclose { background-position:-24px 0px; }
.iexec { background-position:-48px 0px; }
.ihistory, .ichangeuser { background-position:-24px -24px; }
.isend { background-position:-48px -24px; }
.issl { background-position:-173px 0px; }
.isound { background-position:-149px 0px; }
.inosound { background-position:-149px -24px; }
.iemail { background-position:0px 0px; }
.iclosewin { background-position:-108px -34px; width: 15px; height: 15px; }
.ibuttondiv { background-position:-73px -2px; width: 35px; height: 45px;}
.isubmit { background-position:-108px 0px; width: 40px; height: 35px; }
.isubmitrest { background-position:-139px 0px; width: 10px; height: 35px;}
.tplimageloc {
	background: transparent url(${mibewroot}${url:image.chat.sprite}) no-repeat scroll 0px 0px;
	-moz-background-clip: -moz-initial; 
	-moz-background-origin: -moz-initial; 
	-moz-background-inline-policy: -moz-initial;
}
.ilog { background-position: 0px 0px;width: 20px; height: 80px; }
.imessage { background-position: 0px -82px;width: 20px; height: 85px; }
</style>
</head>
<body bgcolor="#FFFFFF" style="background-image: url(${tplroot}/images/bg.gif); margin: 0px;" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400">

<table width="100%" cellspacing="0" cellpadding="0" border="0" style="height: 100%;">
<tr>
<td valign="top">

	<table width="100%" cellspacing="0" cellpadding="0" border="0" style="height: 100%;">
	<tr>
    <td></td>
    <td colspan="2" height="100" style="background-image: url(${tplroot}/images/banner.png);" valign="top" class="bgrn">
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
		    		${if:mibewHost}
		            	<a onclick="window.open('${page:mibewHost}');return false;" href="${page:mibewHost}">
			            	<img src="${page:ct.company.chatLogoURL}" border="0" alt="">
			            </a>
			        ${else:mibewHost}
		            	<img src="${page:ct.company.chatLogoURL}" border="0" alt="">
			        ${endif:mibewHost}
		        ${endif:ct.company.chatLogoURL}
		    </td>
			</tr>
			<tr>
		    <td height="5"></td>
			</tr>
            ${ifnot:ct.company.chatLogoURL}
			<tr>
		    <td align="center" class="text">
	    		${if:mibewHost}
	            	<a onclick="window.open('${page:mibewHost}');return false;" href="${page:mibewHost}">${page:ct.company.name}</a>
			    ${else:mibewHost}
			    	${page:ct.company.name}
			    ${endif:mibewHost}
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
			    <td><a class="closethread" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.close_title}"><img class="tplimage iclosewin" src="${mibewroot}/images/free.gif" border="0" alt="${msg:chat.window.close_title}"/></a></td>
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
				<a href="${page:historyParamsLink}" target="_blank" title="${msg:page.analysis.userhistory.title}" onclick="this.newWindow = window.open('${page:historyParamsLink}', 'UserHistory', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=703,height=380,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;">${page:ct.user.name}</a>
			${else:historyParams}
				${msg:chat.window.chatting_with} <b>${page:ct.user.name}</b>
			${endif:historyParams}
				</td>
${endif:agent}
${if:user}
	${if:canChangeName}
				<td class="text" nowrap>
				<div id="changename1" style="display:${page:displ1};">
					<table cellspacing="0" cellpadding="0" border="0"><tr>
					<td class="text" nowrap>${msg:chat.client.name}</td>
					<td width="10" valign="top"><img src="${mibewroot}/images/free.gif" width="10" height="1" border="0" alt="" /></td>
					<td><input id="uname" type="text" size="12" value="${page:ct.user.name}" class="field"></td>
					<td width="5" valign="top"><img src="${mibewroot}/images/free.gif" width="5" height="1" border="0" alt="" /></td>
					<td><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}"><img class="tplimage iexec" src="${mibewroot}/images/free.gif" border="0" alt="&gt;&gt;" /></a></td>
					</tr></table>
				</div>
				<div id="changename2" style="display:${page:displ2};">
					<table cellspacing="0" cellpadding="0" border="0"><tr>
					<td class="text" nowrap><a id="unamelink" href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}">${page:ct.user.name}</a></td>
					<td width="10" valign="top"><img src="${mibewroot}/images/free.gif" width="10" height="1" border="0" alt="" /></td>
					<td><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}"><img class="tplimage ichangeuser" src="${mibewroot}/images/free.gif" border="0" alt="" /></a></td>
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
				<td width="10" valign="top"><img src="${mibewroot}/images/free.gif" width="10" height="1" border="0" alt="" /></td>
				<td><a class="closethread" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.close_title}">
				<img class="tplimage iclose" src="${mibewroot}/images/free.gif" border="0" alt="${msg:chat.window.close_title}"/></a></td>
${endif:agent}

			    <td><img class="tplimage ibuttondiv" src="${mibewroot}/images/free.gif" border="0" alt="" /></td>
${if:user}
				<td><a href="${page:mailLink}&amp;style=${styleid}" target="_blank" title="${msg:chat.window.toolbar.mail_history}" onclick="this.newWindow = window.open('${page:mailLink}&amp;style=${styleid}', 'ForwardMail', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=603,height=254,resizable=0'); if (this.newWindow != null) {this.newWindow.focus();this.newWindow.opener=window;}return false;"><img class="tplimage iemail" src="${mibewroot}/images/free.gif" border="0" alt="Mail&nbsp;"/></a></td>
${endif:user}
${if:agent}
${if:canpost}
				<td><a href="${page:redirectLink}&amp;style=${styleid}" title="${msg:chat.window.toolbar.redirect_user}">
				<img class="tplimage isend" src="${mibewroot}/images/free.gif" border="0" alt="Redirect&nbsp;" /></a></td>
${endif:canpost}
${if:historyParams}
				<td><a href="${page:historyParamsLink}" target="_blank" title="${msg:page.analysis.userhistory.title}" onclick="this.newWindow = window.open('${page:historyParamsLink}', 'UserHistory', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=720,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img class="tplimage ihistory" src="${mibewroot}/images/free.gif" border="0" alt="History&nbsp;"/></a></td>
${endif:historyParams}
${endif:agent}
				<td><a id="togglesound" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.toolbar.toggle_sound}">
				<img id="soundimg" class="tplimage isound" src="${mibewroot}/images/free.gif" border="0" alt="Sound&nbsp;" /></a></td>

				<td><a id="refresh" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.toolbar.refresh}">
				<img class="tplimage irefresh" src="${mibewroot}/images/free.gif" border="0" alt="Refresh&nbsp;" /></a></td>
${if:sslLink}
				<td><a href="${page:sslLink}&amp;style=${styleid}" title="SSL" >
				<img class="tplimage issl" src="${mibewroot}/images/free.gif" border="0" alt="SSL&nbsp;"/></a></td>
${endif:sslLink}
				<td width="20" valign="top"><img src="${mibewroot}/images/free.gif" width="20" height="1" border="0" alt="" /></td>
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

		<table width="100%" cellspacing="0" cellpadding="0" border="0" style="height:100%;">
		<tr>
	    <td width="20" valign="top"><img class="tplimageloc ilog" src="${mibewroot}/images/free.gif" border="0" alt="" /></td>
    	<td colspan="2" width="100%" height="100%" valign="top" id="chatwndtd">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" style="height:100%;">
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td bgcolor="#A1A1A1"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td>
		    <td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
				<iframe id="chatwnd" width="100%" height="100%" src="${if:neediframesrc}${mibewroot}/images/blank.html${endif:neediframesrc}" frameborder="0" style="overflow:auto;">
				Sorry, your browser does not support iframes; try a browser that supports W3 standards.
				</iframe>
			</td>
		    <td bgcolor="#A1A1A1"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td>
			</tr>
			</table>
		</td>
		</tr>
${if:canpost}
		<tr>
	    <td colspan="3" height="5" style="cursor:n-resize;" id="spl1"></td>
		</tr>

		<tr>
	    <td width="20" valign="top"><img class="tplimageloc imessage" src="${mibewroot}/images/free.gif" border="0" alt="" /></td>
	    ${if:isOpera95}
    	<td width="100%" height="60%" valign="top" id="msgwndtd">
    	${else:isOpera95}
    	<td width="100%" height="100" valign="top" id="msgwndtd">
    	${endif:isOpera95}
			<table width="100%" cellspacing="0" cellpadding="0" border="0" style="height:100%;"><tr><td colspan="3" bgcolor="#A1A1A1"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td bgcolor="#A1A1A1"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
				<textarea id="msgwnd" class="message" tabindex="0"></textarea>
			</td><td bgcolor="#A1A1A1"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td colspan="3" bgcolor="#A1A1A1"><img src="${mibewroot}/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr></table>
		</td>
	    <td valign="middle" id="avatarwnd"></td>
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
		<td align="center" class="copyr">${msg:chat.window.poweredby} <a id="poweredByLink" href="http://mibew.org" title="Mibew Community" target="_blank">mibew.org</a></td>
		<td align="right">

${if:canpost}
			<table cellspacing="0" cellpadding="0" border="0" id="postmessage">

			<tr>

		    <td><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}"><img class="tplimage isubmit" src="${mibewroot}/images/free.gif" border="0" alt="" /></a></td>
		    <td style="background-image: url(${mibewroot}/images/submitbg.gif);" valign="top" class="submit">
				<img src="${mibewroot}/images/free.gif" width="1" height="10" border="0" alt="" /><br/>
				<a id="sndmessagelnk" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}">${msg:chat.window.send_message_short,send_shortcut}</a><br/>
			</td>
			<td width="10"><a href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}"><img class="tplimage isubmitrest" src="${mibewroot}/images/free.gif" border="0" alt="" /></a></td>
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
    <td width="10"><img src="${mibewroot}/images/free.gif" width="10" height="1" border="0" alt="" /></td>
    <td width="100%"><img src="${mibewroot}/images/free.gif" width="585" height="1" border="0" alt="" /></td>
    <td width="5"><img src="${mibewroot}/images/free.gif" width="5" height="1" border="0" alt="" /></td>
	</tr>
	</table>

</td>
</tr>
</table>

</body>
</html>

