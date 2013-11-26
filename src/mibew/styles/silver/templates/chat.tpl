<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title>${msg:chat.window.title.agent}</title>	
	<link rel="shortcut icon" href="${mibewroot}/images/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" media="all" />
	<script type="text/javascript" src="${mibewroot}/js/${jsver}/common.js"></script>
	<script type="text/javascript" src="${mibewroot}/js/${jsver}/brws.js"></script>
	<script type="text/javascript">
		<!--
		var threadParams = {
			servl:"${mibewroot}/thread.php",wroot:"${mibewroot}",frequency:${page:frequency},${if:user}user:"true",${endif:user}threadid:${page:ct.chatThreadId},token:${page:ct.token},cssfile:"${tplroot}/chat.css",ignorectrl:${page:ignorectrl}
		};
		var stxt = 10;
		function getClientHeight() {
			return document.compatMode=='CSS1Compat' || !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
		}
		function getClientWidth() {
			return document.compatMode=='CSS1Compat' || !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
		}
		function setTrueHeight() {
			chatHeight = getClientHeight();
			someHeight = chatHeight-document.getElementById("top").offsetHeight-document.getElementById("chatheader").offsetHeight-document.getElementById("message").offsetHeight-document.getElementById("send").offsetHeight-1;
			document.getElementById("chat").style.height = (someHeight) + "px";
			document.getElementById("chatwnd").style.height = (someHeight-38) + "px";
			${if:user}
				document.getElementById("avatar-wrapper").style.height = (someHeight-39) + "px";
			${endif:user}

			chatWidth = getClientWidth();
			${if:user}
				someWidth = chatWidth-28-120;
			${else:user}
				someWidth = chatWidth-28;
			${endif:user}
			document.getElementById("chatwnd").style.width = (someWidth) + "px";
		}
		function enlargeFontSize() {
			stxt += 2;
			if (stxt > 14) {
				stxt = 14;
			}
			window.chatwnd.document.getElementById("content").style.fontSize = (stxt) + "px";
		}
		function reduceFontSize() {
			stxt -= 2;
			if (stxt < 8) {
				stxt = 8;
			}
			window.chatwnd.document.getElementById("content").style.fontSize = (stxt) + "px";
		}
		window.onresize = setTrueHeight;		
		//-->
	</script>
	<script type="text/javascript" src="${mibewroot}/js/${jsver}/chat.js"></script>
</head>
<body class="body">
	<div id="top">
		<div id="logo">
			${if:ct.company.chatLogoURL}
				${if:mibewHost}
					<a onclick="window.open('${page:mibewHost}');return false;" href="${page:mibewHost}">
						<img onload="setTrueHeight();" src="${page:ct.company.chatLogoURL}" alt=""/>
					</a>
				${else:mibewHost}
					<img onload="setTrueHeight();" src="${page:ct.company.chatLogoURL}" alt=""/>
				${endif:mibewHost}
			${else:ct.company.chatLogoURL}
				${if:mibewHost}
					<a onclick="window.open('${page:mibewHost}');return false;" href="${page:mibewHost}">
						<img onload="setTrueHeight();" src="${tplroot}/images/default-logo.png" alt=""/>
					</a>
				${else:mibewHost}
					<img onload="setTrueHeight();" src="${tplroot}/images/default-logo.png" alt=""/>
				${endif:mibewHost}
			${endif:ct.company.chatLogoURL}
			&nbsp;
			<div id="page-title">${page:chat.title}</div>
			<div class="clear">&nbsp;</div>
		</div>
	</div>
	<div id="chatheader">
		<div class="bgc"><div class="bgl"><div class="bgr">
			${if:agent}
				<div id="changename2" class="agent">
					${if:historyParams}
						${msg:chat.window.chatting_with}
						<a href="${page:historyParamsLink}" target="_blank" title="${msg:page.analysis.userhistory.title}" onclick="this.newWindow = window.open('${page:historyParamsLink}', 'UserHistory', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=703,height=380,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;">${page:ct.user.name}</a>
					${else:historyParams}
						${msg:chat.window.chatting_with} <strong>${page:ct.user.name}</strong>
					${endif:historyParams}
				</div>
			${endif:agent}
			${if:user}
				${if:canChangeName}
					<div id="changename1" style="display:${page:displ1};">
						<div class="you">${msg:chat.client.name}</div>
						<div class="input-name"><input id="uname" type="text" size="12" value="${page:ct.user.name}" class="username" /></div>
						<a class="changename" href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}"><img class="tplimage iexec" src="${mibewroot}/images/free.gif" alt="&gt;&gt;" /></a>
					</div>
					<div id="changename2" style="display:${page:displ2};">
						<div class="you2">${msg:chat.client.name}</div>
						<a id="unamelink" href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}">${page:ct.user.name}</a>
						<a class="changename" href="javascript:void(0)" onclick="return false;" title="${msg:chat.client.changename}"><img class="tplimage ichangeuser" src="${mibewroot}/images/free.gif" alt="" /></a>
					</div>
				${else:canChangeName}
					<div id="changename1"><div id="you">${msg:chat.client.name}&nbsp;${page:ct.user.name}</div></div>
				${endif:canChangeName}
			${endif:user}
			<div class="buttons">
				<a href="javascript:void(0)" onclick="reduceFontSize();"><img class="tplimage fontreduce" src="${mibewroot}/images/free.gif" alt="Reduce font&nbsp;" /></a>
				<a href="javascript:void(0)" onclick="enlargeFontSize();"><img class="tplimage fontenlarge" src="${mibewroot}/images/free.gif" alt="Enlarge font&nbsp;" /></a>
				<img class="empty" src="${mibewroot}/images/free.gif" alt="" />
				${if:user}
					<a href="${page:mailLink}&amp;style=${styleid}" target="_blank" title="${msg:chat.window.toolbar.mail_history}" onclick="this.newWindow = window.open('${page:mailLink}&amp;style=${styleid}', 'ForwardMail', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=603,height=254,resizable=0'); if (this.newWindow != null) {this.newWindow.focus();this.newWindow.opener=window;}return false;"><img class="tplimage iemail" src="${mibewroot}/images/free.gif" alt="Mail&nbsp;"/></a>
				${endif:user}
				${if:agent}
					${if:canpost}
						<a href="${page:redirectLink}&amp;style=${styleid}" title="${msg:chat.window.toolbar.redirect_user}"><img class="tplimage isend" src="${mibewroot}/images/free.gif" alt="Redirect&nbsp;" /></a>
					${endif:canpost}
					${if:historyParams}
						<a href="${page:historyParamsLink}" target="_blank" title="${msg:page.analysis.userhistory.title}" onclick="this.newWindow = window.open('${page:historyParamsLink}', 'UserHistory', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=720,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img class="tplimage ihistory" src="${mibewroot}/images/free.gif" alt="History&nbsp;"/></a>
					${endif:historyParams}
				${endif:agent}
				<a id="togglesound" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.toolbar.toggle_sound}"><img id="soundimg" class="tplimage isound" src="${mibewroot}/images/free.gif" alt="Sound&nbsp;" /></a>
				<a id="refresh" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.toolbar.refresh}"><img class="tplimage irefresh" src="${mibewroot}/images/free.gif" alt="Refresh&nbsp;" /></a>
				${if:sslLink}
					<a href="${page:sslLink}&amp;style=${styleid}" title="SSL" ><img class="tplimage issl" src="${mibewroot}/images/free.gif" alt="SSL&nbsp;"/></a>
				${endif:sslLink}
				<a class="closethread" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.close_title}"><img class="tplimage iclosewin" src="${mibewroot}/images/free.gif" alt="${msg:chat.window.close_title}"/></a>
			</div>
		</div></div></div>
	</div>
	<div id="chat">
		<div class="bgl"><div class="bgr"><div class="sdwbgc"><div class="sdwbgl"><div class="sdwbgr">
			<iframe onload="setTrueHeight();" id="chatwnd" name="chatwnd" src="${if:neediframesrc}${mibewroot}/images/blank.html${endif:neediframesrc}" frameborder="0" style="overflow:auto;">
				Sorry, your browser does not support iframes; try a browser that supports W3 standards.
			</iframe>
			<div id="inf">
				<div id="engineinfo" style="display:none;"></div>
				<div id="typingdiv" style="display:none;">${msg:typing.remote}</div>
			</div>
			${if:user}
				<div id="avatar-wrapper">
					<div id="avatarwnd">&nbsp;</div>
				</div>
			${endif:user}
			</div></div></div></div></div>
	</div>
	<div id="message">
	${if:canpost}
		<div class="bgc"><div class="bgl"><div class="bgr">
			<textarea id="msgwnd" class="message" tabindex="0" rows="4" cols="10"></textarea>
		</div></div></div>
	${endif:canpost}
	</div>
	<div id="send">
	${if:canpost}
		<div id="postmessage">
			<div id="predefined-wrapper">
				${if:agent}
					<select id="predefined" size="1" class="answer">
					<option>${msg:chat.window.predefined.select_answer}</option>
					${page:predefinedAnswers}
					</select>
				${endif:agent}
			</div>
			<a id="sndmessagelnk" href="javascript:void(0)" onclick="return false;" title="${msg:chat.window.send_message}">${msg:chat.window.send_message_short,send_shortcut}</a>
			<div class="clear">&nbsp;</div>
		</div>
	${endif:canpost}
		<div id="footer">${msg:chat.window.poweredby} <a id="poweredByLink" href="http://mibew.org" title="Mibew Community" target="_blank">mibew.org</a></div>
	</div>
</body>
</html>