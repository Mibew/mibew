<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:chat.window.title.agent}</title>
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
			<td nowrap="nowrap" style="padding-right:10px"><span style="font-size:16px;font-weight:bold;color:#525252">${msg:chat.redirect.title}</span></td>
		</tr>
		</table>
			<table cellspacing="0" cellpadding="0" border="0" id="header" class="bg_domain">
			<tr>
				<td style="padding-left:20px;width:612px;color:white;" class="mmimg">
					${msg:chat.redirect.choose}
				</td>
				<td align="right" style="padding-right:17px;">
					<table cellspacing="0" cellpadding="0" border="0">
					<tr>
					<td><a href="javascript:window.close();" title="${msg:chat.redirect.back}"><img src='${tplroot}/images/buttons/back.gif' width="25" height="25" border="0" alt="" /></a></td>
					<td width="5"></td>
					<td class="button"><a href="javascript:window.back();" title="${msg:chat.redirect.back}">${msg:chat.redirect.back}</a></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
	</td>
</tr>
<tr>
	<td valign="top" style="padding:0px 0px 0px 24px;">

			<table width="100%" cellpadding="0">
			<tr>
	    	<td width="50%" valign="top">
${if:redirectToAgent}
	    	${msg:chat.redirect.operator}<br/>
	    	<ul class="agentlist">
		    	${page:redirectToAgent}
	    	</ul>
${endif:redirectToAgent}
	    	</td>
	    	<td width="50%" valign="top">
${if:redirectToGroup}
	    	${msg:chat.redirect.group}<br/>
	    	<ul class="agentlist">
		    	${page:redirectToGroup}
	    	</ul>
${endif:redirectToGroup}
	    	</td>
	    	</tr></table>

	</td>
</tr>
<tr>
	<td valign="top" style="padding:24px">
		${pagination}
	</td>
</tr>
</table>


</td>
</tr>
</table>
</body>
</html>
