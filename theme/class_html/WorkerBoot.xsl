<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="WorkerBoot">
<html>
<head></head>
<body>
	<h1><a>
		<xsl:attribute name="href">/WorkerBoot/<xsl:value-of select="ID" />.html</xsl:attribute>
		<xsl:value-of select="user/User/nickname" />
	</a></h1>
	<ul>
		<li>
			<xsl:choose>
				<xsl:when test="/user/User/loggedin">
					<span style="color:green;">Available</span>
				</xsl:when>
				<xsl:otherwise>
					<span style="color:red;">Unavailable</span>
				</xsl:otherwise>
			</xsl:choose>
		</li>
		<li>
			<h2>Role: <xsl:value-of select="role" /></h2>
		</li>
		<li>
			<xsl:if test="not(current_clockin = '')">
				<a>
				<xsl:attribute name="href">DevProject/<xsl:value-of select="current_clockin/project" />.html</xsl:attribute>
				Checkout what they're working on!
				</a>
			</xsl:if>
		</li>
	</ul>
</body>
</html>
</xsl:template>
</xsl:stylesheet>