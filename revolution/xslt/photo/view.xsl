<xsl:template match="/social[@template='photo.view']">
    <xsl:call-template name="zoomin" />
</xsl:template>

<xsl:template match="/social[@template='photo.view']//entry">
    <a class="xbutton" href="photos">&#171;<span><span>&#9650;</span>πίσω στις εικόνες</span></a>
    <div class="contentitem">
        <xsl:attribute name="id">photo_<xsl:value-of select="/social/entry/@id" /></xsl:attribute>
        <div class="details">
            <ul>
                <li>
                    <xsl:apply-templates select="author" />
                </li>
                <li><div class="time"><xsl:value-of select="published" /></div></li>
                <xsl:if test="favourites[1]/@count &gt; 0">
                    <li class="stat numfavourites">&#9829; <span><xsl:value-of select="favourites[1]/@count" /></span></li>
                </xsl:if>
                <xsl:if test="discussion[1]/@count &gt; 0">
                    <li class="stat numcomments"><span><xsl:value-of select="discussion[1]/@count" /></span></li>
                </xsl:if>
            </ul>
        </div>
        <img class="maincontent">
            <xsl:attribute name="src">
                <xsl:value-of select="media[1]/@url" />
            </xsl:attribute>
            <xsl:attribute name="width">
                <xsl:value-of select="media[1]/@width" />
            </xsl:attribute>
            <xsl:attribute name="height">
                <xsl:value-of select="media[1]/@height" />
            </xsl:attribute>
        </img>
        <span class="title">
            <xsl:value-of select="title[1]" />
        </span>
        <div class="note">
            <xsl:for-each select="favourites/user">
                <div class="love">&#9829; <span class="username"><xsl:value-of select="name[1]" /> </span> </div>
            </xsl:for-each>
            <a class="love button" href="" style="display:none"><strong>&#9829;</strong> Το αγαπώ!</a>
        </div>
    </div>
    <xsl:apply-templates select="discussion" />
    <script type="text/javascript">
    Startup( function () {
        ItemView.Init( 2 );
    } );
    </script>
</xsl:template>
