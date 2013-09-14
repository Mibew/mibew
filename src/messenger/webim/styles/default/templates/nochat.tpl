<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:chat.window.title.user}</title>
<link rel="shortcut icon" href="${mibewroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css"/>
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
</style>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" style="margin:0px">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td valign="top" style="padding:5px">
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
			<td nowrap="nowrap" style="padding-right:10px"><span style="font-size:16px;font-weight:bold;color:#525252">${msg:page.chat.old_browser.title}</span></td>
		</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="0" id="header" class="bg_domain">
		<tr>
			<td style="padding-left:20px;color:white;" class="mmimg" width="770">
				${msg:page.chat.old_browser.problem}
			</td>
			<td align="right" style="padding-right:17px;padding-left:17px;">
				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td><a href="javascript:window.close();" title="${msg:page.chat.old_browser.close}"><img src="${tplroot}/images/buttons/back.gif" width="25" height="25" border="0" alt="" /></a></td>
					<td width="5"></td>
					<td class="button"><a href="javascript:window.close();" title="${msg:page.chat.old_browser.close}">${msg:page.chat.old_browser.close}</a></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		${msg:page.chat.old_browser.list}

	</td>
</tr>
</table>
</body>
</html>