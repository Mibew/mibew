<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:chat.window.title.user}</title>
<link rel="shortcut icon" href="${mibewroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
<style type="text/css">
#header{
	height:50px;
	background:url(${tplroot}/images/bg_domain.gif) repeat-x top;
	background-color:#5AD66B;
	width:99.6%;
	margin:0px 0px 20px 0px;
}
.but{
	font-family:Verdana !important;
	font-size:11px;
	background:url(${tplroot}/images/butbg.gif) no-repeat top left;
	display:block;
	text-align:center;
	padding-top:2px;
	color:white;
	width:80px;
	height:18px;
	text-decoration:none;
	position:relative;top:1px;
}
</style>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" style="margin:0px">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td valign="top" style="padding: 5px;">

		<form name="mailThreadForm" method="post" action="${mibewroot}/mail.php"><input type="hidden" name="style" value="${styleid}"/>
		<input type="hidden" name="thread" value="${page:ct.chatThreadId}"/><input type="hidden" name="token" value="${page:ct.token}"/><input type="hidden" name="level" value="${page:level}"/>

		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td width="100%" height="100" style="padding-left:20px;">
			    	${if:ct.company.chatLogoURL}
			    		${if:mibewHost}
			            	<a onclick="window.open('${page:mibewHost}');return false;" href="${page:mibewHost}">
				            	<img src="${page:ct.company.chatLogoURL}" border="0" alt=""/>
				            </a>
				        ${else:mibewHost}
			            	<img src="${page:ct.company.chatLogoURL}" border="0" alt=""/>
				        ${endif:mibewHost}
				    ${else:ct.company.chatLogoURL}
	    				${if:mibewHost}
	        	    		<a onclick="window.open('${page:mibewHost}');return false;" href="${page:mibewHost}">
	        		    		<img src="${mibewroot}/images/mibewlogo.gif" border="0" alt=""/>
	        		    	</a>
					    ${else:mibewHost}
					    	<img src="${mibewroot}/images/mibewlogo.gif" border="0" alt=""/>
					    ${endif:mibewHost}
			        ${endif:ct.company.chatLogoURL}
				</td>
				<td nowrap="nowrap" style="padding-right:10px"><span style="font-size:16px;font-weight:bold;color:#525252">${msg:mailthread.title}</span></td>
			</tr>
		</table>
${if:errors}
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td valign="top"><img src="${mibewroot}/images/icon_err.gif" width="40" height="40" border="0" alt="" /></td>
	    <td width="10"></td>
	    <td class="text">
		    ${errors}
		</td>
		</tr>
		</table>
${endif:errors}
		<table cellspacing="0" cellpadding="0" border="0" id="header" class="bg_domain">
			<tr>
				<td style="padding-left: 20px" class="img132" width="270">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td class="text" style="color: white" nowrap="nowrap">${msg:mailthread.enter_email}</td>
						<td width="10"></td>
						<td><input type="text" name="email" size="20" value="${form:email}" class="username" /></td>
					</tr>
				</table>
				</td>
				<td align="left">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td><a href="javascript:document.mailThreadForm.submit();"
							class="but" id="sndmessagelnk">${msg:mailthread.perform}</a></td>
					</tr>
				</table>
				</td>
				<td align="right" style="padding-right: 17px;">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td><a href="javascript:window.close();" title="${msg:mailthread.close}"><img
							src="${tplroot}/images/buttons/back.gif" width="25" height="25"
							border="0" alt="" /></a></td>
						<td width="5"></td>
						<td class="button"><a href="javascript:window.close();"
							title="${msg:mailthread.close}">${msg:mailthread.close}</a></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>


		<table width="100%" cellspacing="0" cellpadding="0"
			border="0">
			<tr>
				<td height="60"></td>
				<td></td>
				<td></td>
			</tr>

			<tr>
				<td height="70"></td>
				<td>

				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="50%"></td>
						<td width="50%" align="right"></td>
					</tr>
				</table>

				</td>
				<td></td>
			</tr>

			<tr>
				<td width="30"><img src="${mibewroot}/images/free.gif" width="30" height="1" border="0" alt="" /></td>
				<td width="100%"><img src="${mibewroot}/images/free.gif" width="540" height="1" border="0" alt="" /></td>
				<td width="30"><img src="${mibewroot}/images/free.gif" width="30" height="1" border="0" alt="" /></td>
			</tr>
		</table>

		</form>
	</td>
</tr>
</table>
</body>
</html>
