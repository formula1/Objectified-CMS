<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html"
              encoding="UTF-8"
              indent="yes" />





<xsl:template match="/">
&lt; !DOCTYPE html>
<html>
<head>
<title>Sam Tobias Website</title>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<link rel="stylesheet" type="text/css" href="/theme/theme.css" />
<script type="text/javascript" src="/theme/all/jquery.js" > </script>
<script type="text/javascript" src="/theme/fractals/phytree.js"></script>
<script type="text/javascript" src="/Core/App/public/applictaion-manager-js.php" > </script>
<canvas class="bg phy_tree"><img class="phy_tree" src="/theme/fractals/pyth_tree.php" /></canvas>

<header class="inline">
<nav id="menu-main">
	<ul class="vertical">
	<xsl:apply-templates select="nav" />
	</ul>
</nav>
</header>
<section class="inline">
	<section class="featured">
		<ul class="stacked">
			<xsl:apply-templates select="featured" />
		</ul>
	</section>
</section>
<footer>
</footer>
</xsl:template>

<xsl:template match="nav">
<xsl:variable name="item" select="/items/item[text()]"/>

<li>
<a>
	<xsl:attribute name="href"><xsl:value-of select="$item/url" /></xsl:attribute>
	<img>
		<xsl:attribute name="src">
		<xsl:choose>
			<xsl:when test="count($item/icon) &gt; 0">
				<xsl:value-of select="$item/icon/source" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="/default/nav/icon/source" />
			</xsl:otherwise>
		</xsl:choose>
		</xsl:attribute>
	</img><br />
	<span class="menu-title"><xsl:value-of select="$item/title" /></span>
</a>
</li>
</xsl:template>


<xsl:template match="featured-item">
  <xsl:variable name="item" select="/items/item[text()]"/>
<li>
	<h1 class="title"><a>
			<xsl:attribute name="href"><xsl:value-of select="$item/url" /></xsl:attribute>
			<xsl:value-of select="$item/content/title" />
		</a></h1>
	<img class="bg">
		<xsl:attribute name="src">
			<xsl:choose>
				<xsl:when test="count($item/image) &gt; 0">
					<xsl:value-of select="$item/image/source" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="/default/featured/image/source" />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
	</img>
	<xsl:if test="count($item/description) &gt; 0">
		<p><xsl:value-of select="$item/description" /></p>
	</xsl:if>
	<xsl:if text="count($item/related) &gt; 0">
		<ul style="horizontal">
		<xsl:for-each select="$item/related/*" >
			<li>
				<a>
				<xsl:attribute name="href"><xsl:value-of select="$item/url" /></xsl:attribute>
				<img class="icon">
					<xsl:attribute name="src">
						<xsl:choose>
							<xsl:when test="count($item/icon) &gt; 0">
								<xsl:value-of select="$item/icon/source" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="/default/featured/icon/source" />
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
				</img>
				</a>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="$item/url" /></xsl:attribute>
					<xsl:value-of select="$item/title" />
				</a>
			</li>
		</xsl:for-each>
		</ul>
	</xsl:if>
	
</li>
</featured>
</xsl:stylesheet>