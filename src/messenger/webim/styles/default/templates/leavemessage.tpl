<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:leavemessage.title}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
<style type="text/css">
#header{
	height:50px;
	background:url(${tplroot}/images/bg_domain.gif) repeat-x top;
	background-color:#5AD66B;
	width:99.6%;
	margin:0px 0px 20px 0px;
}
#header .mmimg{
	background:url(${tplroot}/images/quadrat.gif) bottom left no-repeat;
}
.form td{
	background-color:#f4f4f4;
	color:#525252;
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

<body bgcolor="#FFFFFF" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" style="margin:0px;">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">


<form name="leaveMessageForm" method="post" action="${webimroot}/leavemessage.php">
<input type="hidden" name="style" value="${styleid}"/>
<input type="hidden" name="info" value="${form:info}"/>
<input type="hidden" name="referrer" value="${page:referrer}"/>
${if:formgroupid}<input type="hidden" name="group" value="${form:groupid}"/>${endif:formgroupid}
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td valign="top" height="150" style="padding:5px">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td width="100%" height="100" style="padding-left:20px;">
		    	${if:ct.company.chatLogoURL}
		    		${if:webimHost}
		            	<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
			            	<img src="${page:ct.company.chatLogoURL}" border="0" alt=""/>
			            </a>
			        ${else:webimHost}
		            	<img src="${page:ct.company.chatLogoURL}" border="0" alt=""/>
			        ${endif:webimHost}
			    ${else:ct.company.chatLogoURL}
	    			${if:webimHost}
	        	    	<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
	        	    		<img src="${webimroot}/images/webimlogo.gif" border="0" alt=""/>
	        	    	</a>
				    ${else:webimHost}
				    	<img src="${webimroot}/images/webimlogo.gif" border="0" alt=""/>
				    ${endif:webimHost}
		        ${endif:ct.company.chatLogoURL}
			</td>
			<td nowrap="nowrap" style="padding-right:10px"><span style="font-size:16px;font-weight:bold;color:#525252">${if:formgroupname}${form:groupname}: ${endif:formgroupname}${msg:leavemessage.title}</span></td>
		</tr>
		</table>
			<table cellspacing="0" cellpadding="0" border="0" id="header" class="bg_domain">
			<tr>
				<td style="padding-left:20px;width:612px;color:white;" class="mmimg">
					${msg:leavemessage.descr}
				</td>
				<td align="right" style="padding-right:17px;">
					<table cellspacing="0" cellpadding="0" border="0">
					<tr>
					<td><a href="javascript:window.close();" title="${msg:leavemessage.close}"><img src='${tplroot}/images/buttons/back.gif' width="25" height="25" border="0" alt="" /></a></td>
					<td width="5"></td>
					<td class="button"><a href="javascript:window.close();" title="${msg:leavemessage.close}">${msg:leavemessage.close}</a></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
	</td>
</tr>
<tr>
	<td valign="top" style="padding:0px 24px;">
${if:errors}
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td valign="top"><img src='${webimroot}/images/icon_err.gif' width="40" height="40" border="0" alt="" /></td>
	    <td width="10"></td>
	    <td class="text">
		    ${errors}
		</td>
		</tr>
		</table>
${endif:errors}
		<table cellspacing="1" cellpadding="5" border="0" class="form">
			<tr>
				<td class="text">${msg:form.field.email}:</td>
				<td><input type="text" name="email" size="50" value="${form:email}" class="username"/></td>
			</tr>
			<tr>
				<td class="text">${msg:form.field.name}:</td>
				<td><input type="text" name="name" size="50" value="${form:name}" class="username"/></td>
			</tr>
			<tr>
				<td class="text">${msg:form.field.message}:</td>
				<td valign="top">
					<textarea name="message" tabindex="0" cols="40" rows="5" style="border:1px solid #878787; overflow:auto">${form:message}</textarea>
				</td>
			</tr>
${if:showcaptcha}
			<tr>
				<td class="text"><img src="captcha.php"/></td>
				<td><input type="text" name="captcha" size="50" maxlength="15" value="" class="username"/></td>
			</tr>
${endif:showcaptcha}
			<tr>
				<td colspan="2" align="right">
					<table cellspacing="0" cellpadding="0" border="0">
					<tr>
					<td><a href="javascript:document.leaveMessageForm.submit();" class="but" id="sndmessagelnk">${msg:mailthread.perform}</a></td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<table cellpadding="7" cellspacing="5" border="0" width="450">
		<tr>
			<td id="poweredByTD" align="center" class="copyr">
				${msg:chat.window.poweredby} <a id="poweredByLink" href="http://mibew.org" title="Mibew Community" target="_blank">mibew.org</a>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td valign="top" style="padding:24px">
	</td>
</tr>
</table>
</form>

</td>
</tr>
</table>
</body>
</html>
