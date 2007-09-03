<?xml version="1.0" encoding="windows-1251" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="ascii"/>
<xsl:param name="lang"/>

<xsl:template match="hxml">
	<xsl:call-template name="main_layout"/>
</xsl:template>

<!-- ################################################################################### -->

<xsl:template name="main_layout">
<xsl:text disable-output-escaping="yes"><![CDATA[<!-- ERROR: Zero-length file! -->



































































]]></xsl:text><html>
<head>
    <title>Web Messenger</title>
    <link href="styles.css" type="text/css" rel="stylesheet" />
</head>
<body bgcolor="#FFFFFF" link="#2971C1" vlink="#2971C1" alink="#2971C1" marginwidth="0"
    marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<a name="top"></a>

<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr><td height="95%" valign="top">

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
	  <td width="40"><img src="images/empty.gif" width="40" height="1" border="0" alt="" /></td>
	  <td width="185"><img src="images/empty.gif" width="185" height="1" border="0" alt="" /></td>
	  <td width="30"><img src="images/empty.gif" width="30" height="1" border="0" alt="" /></td>
	  <td width="100%"><img src="images/empty.gif" width="720" height="1" border="0" alt="" /></td>
	  <td width="25"><img src="images/empty.gif" width="25" height="1" border="0" alt="" /></td>
	</tr>

	<tr>
	  <td></td>
	  <td colspan="4" bgcolor="#FFFFFF">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
		  <td width="185" valign="top">
			<table width="185" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td height="35" bgcolor="#FFFFFF"><img src="images/empty.gif" width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
			  <td class="textform" align="center">
   	 	  		<a><xsl:attribute name="href"><xsl:value-of select="i18n/lang[@id=$lang]/home/@link"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="i18n/lang[@id=$lang]/home/text()"/></xsl:attribute><img src="images/logo.gif" width="185" height="140" border="0"><xsl:attribute name="alt"><xsl:value-of select="i18n/lang[@id=$lang]/home/text()"/></xsl:attribute></img></a>
			  </td>
			</tr>
			</table>
  		  </td>
		  <td width="30" valign="top">
			<table width="30" cellspacing="0" cellpadding="0" border="0">
			<tr>
	  		  <td height="45"></td>
			</tr>
			<tr>
			  <td bgcolor="#FFD841"><img src="images/banncrn.gif" width="30" height="140" border="0" alt="" /></td>
			</tr>
			</table>
		  </td>
		  <td width="100%" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
	  		  <td width="50%">
				  <table cellspacing="0" cellpadding="0" border="0">
				  <tr>
<xsl:for-each select="i18n/menu[@lang=$lang]/item[position() &lt; 10]">
	<xsl:if test="position() &gt; 1">
					<td class="textform"><img src="images/topdiv.gif" width="25" height="15" border="0" alt="|" /></td>
	</xsl:if>
					<td class="textform">
						<a><xsl:attribute name="href"><xsl:value-of select="@link"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="@title"/></xsl:attribute><xsl:value-of select="@title"/></a></td>
</xsl:for-each>
				  </tr>
				  </table>
			  </td>
	  		  <td width="120"><img src="images/tmarktop.gif" width="120" height="45" border="0" alt="" /></td>
			  <td width="50%" align="right">
				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
				  <td><a><xsl:attribute name="href"><xsl:value-of select="i18n/lang[@id=$lang]/home/@link"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="i18n/lang[@id=$lang]/home/text()"/></xsl:attribute><img src="images/icon_home.gif" width="15" height="15" border="0"><xsl:attribute name="alt"><xsl:value-of select="i18n/lang[@id=$lang]/home/text()"/></xsl:attribute></img></a></td>
				  <td><img src="images/icondiv.gif" width="55" height="30" border="0" alt="|" /></td>
				  <td><a><xsl:attribute name="href"><xsl:value-of select="i18n/lang[@id=$lang]/mailto/@link"/></xsl:attribute><img src="images/icon_mail.gif" width="15" height="10" border="0"><xsl:attribute name="alt"><xsl:value-of select="i18n/lang[@id=$lang]/mailto/text()"/></xsl:attribute></img></a></td>
				</tr>
				</table>
			  </td>
			</tr>
			<tr>
			  <td>
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
				<tr>
				  <td height="140" width="30%" bgcolor="#FFD841"><img src="images/empty.gif" width="1" height="1" border="0" alt="" /></td>
				  <td width="200" background="images/banntxt.gif" class="bgry">
					<table width="200" cellspacing="0" cellpadding="0" border="0">
					<tr>
					  <td align="right" class="bann"><xsl:value-of select="i18n/lang[@id=$lang]/banner1"/></td>
					</tr>
					<tr>
					  <td height="25" background="images/banntxtdiv.gif"><img src="images/banntxtdiv.gif" width="1" height="25" border="0" alt="" /></td>
					</tr>
					<tr>
					  <td align="right" class="bann"><span class="text"><xsl:value-of select="i18n/lang[@id=$lang]/banner2"/></span></td>
					</tr>
					</table>
				  </td>
				  <td width="70%" background="images/banndiv.gif" class="bgcn"><img src="images/empty.gif" width="1" height="1" border="0" alt="" /></td>
				</tr>
	  			</table>
			  </td>
			  <td><img src="images/tmark.gif" width="120" height="140" border="0" alt="" /></td>
			  <td background="images/bannform.gif" align="center" class="bgry">
				<table width="240" cellspacing="0" cellpadding="0" border="0">
				<tr>
				  <td height="90" background="images/bannformbg.gif" align="center">
					<table cellspacing="0" cellpadding="0" border="0">
					<tr>
					  <td>
					  	<!-- BUTTON -->
					  </td>
					</tr>
					<tr>
					  <td align="right" class="bannform"><xsl:value-of select="i18n/lang[@id=$lang]/button_desc"/></td>
					</tr>
					</table>
				  </td>
				</tr>
				</table>
			  </td>
			</tr>
			<tr>
			  <td></td>
			  <td><img src="images/tmarkbott.gif" width="120" height="10" border="0" alt="" /></td>
			  <td></td>
			</tr>
			</table>
		  </td>
		  <td width="25" valign="top">
			<table width="25" cellspacing="0" cellpadding="0" border="0">
			<tr>
			  <td height="45"></td>
			</tr>
			<tr>
			  <td height="140" background="images/bannr.gif" valign="top"><img src="images/bannr.gif" width="25" height="1" border="0" alt="" /></td>
			</tr>
			</table>
		  </td>
		</tr>
		</table>
	  </td>
	</tr>

	<tr>
	  <td colspan="5" height="10" bgcolor="#FFFFFF"><img src="images/empty.gif" width="1" height="1" border="0" alt=""/></td>
	</tr>

	<tr>
		<td></td>
		<td colspan="4">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td valign="top">
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td height="38"></td>
					</tr>
					<tr>
						<td class="text">
							<xsl:apply-templates select="content"/>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		
		
		</td>
		<td></td>
	</tr>
	</table>
	
</td></tr><tr><td>

	<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td valign="bottom" background="images/grey.gif" style="background-position:bottom;background-repeat:repeat-x;">
		<table width="965" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td width="40"><img src="images/empty.gif" height="1" width="40" border="0" alt=""/></td>
			<td width="100%"><img src="images/empty.gif" height="1" width="900" border="0" alt=""/></td>
			<td width="25"><img src="images/empty.gif" height="1" width="25" border="0" alt=""/></td>
		</tr>
		<tr>
			<td height="40"></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td valign="bottom">
				<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
				<td width="25%">
					<table cellspacing="0" cellpadding="0" border="0"><tr>
						<td>
							<img src="images/icon_address.gif" height="10" width="20" border="0" alt=""/></td>
						<td class="address">
							<a><xsl:attribute name="href"><xsl:value-of select="i18n/lang[@id=$lang]/mailto/@link"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="i18n/lang[@id=$lang]/mailto/text()"/></xsl:attribute><xsl:value-of select="i18n/lang[@id=$lang]/mailto/@friendly" disable-output-escaping="yes"/></a></td>
					</tr>
					</table>
				</td>
				<td width="50%" valign="bottom" align="right">
					<table cellspacing="0" cellpadding="0" border="0"><tr>
					<td>
						<a href="#top"><xsl:attribute name="title"><xsl:value-of select="i18n/lang[@id=$lang]/page_top"/></xsl:attribute>
							<img src="images/butt_top.gif" height="40" width="60" border="0" alt=""/></a></td>
					<td class="address">
						<a href="#top"><xsl:attribute name="title"><xsl:value-of select="i18n/lang[@id=$lang]/page_top"/></xsl:attribute><xsl:value-of select="i18n/lang[@id=$lang]/page_top_short"/></a></td>
					</tr>
					</table>
				</td>
				<td width="25%" align="right">
				</td>
				</tr>
				</table>
			</td>
			<td></td>
		</tr>
		</table>
	</td></tr>
	</table>
</td></tr>
</table>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-1120575-12";
urchinTracker();
</script>

</body>
</html>


</xsl:template>

<!-- ################################################################################### -->

<xsl:template match="text">
	<xsl:value-of select="text()" disable-output-escaping="yes"/>
</xsl:template>

<xsl:template match="h">
	<h3><xsl:apply-templates/></h3>
</xsl:template>

<xsl:template match="i|b|p|br|li|ul">
	<xsl:copy>
		<xsl:apply-templates/>
	</xsl:copy>
</xsl:template>

<xsl:template match="a">
	<xsl:copy><xsl:copy-of select="@*"/>
		<xsl:apply-templates/>
	</xsl:copy>
</xsl:template>

<xsl:template match="lang">
	<xsl:if test="$lang=@lang">
	<xsl:apply-templates/>
	</xsl:if>
</xsl:template>

<!-- ################################################################################### -->

<!-- DOWNLOAD -->

<xsl:template match="download">
<xsl:variable name="dwlddir" select="@dir"/>

<h3><xsl:value-of select="@title"/></h3>

<xsl:for-each select="group">
<p>
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td width="20"><img src="images/empty.gif" height="1" width="20" border="0" alt=""/></td>
<td width="200"><img src="images/empty.gif" height="1" width="200" border="0" alt=""/></td>
<td width="100%"><img src="images/empty.gif" height="1" width="200" border="0" alt=""/></td>
</tr>
<tr><td colspan="3" class="loadh"><xsl:value-of select="@name"/></td></tr>
<tr><td colspan="3" height="5"></td></tr>
<xsl:for-each select="file">
<tr>
<td align="center"><img src="images/dir-file.png" border="0"/></td>
<td><a><xsl:attribute name="href"><xsl:choose><xsl:when test="@link"><xsl:value-of select="@link"/></xsl:when><xsl:otherwise><xsl:value-of select="$dwlddir"/><xsl:value-of select="@name"/></xsl:otherwise></xsl:choose></xsl:attribute><xsl:value-of select="@name"/></a></td>
<td><xsl:value-of select="."/></td>
</tr>
</xsl:for-each>
</table>
</p>
</xsl:for-each>

</xsl:template>

</xsl:stylesheet>
