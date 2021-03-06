<xsl:template match="/social[@resource='favourite' and @method='listing']">
    <xsl:for-each select="favourites">
        <div class="userpage favourites">
            <div class="userdetails">
                <xsl:apply-templates select="author" />
            </div>
            <h2>
                Αγαπημένα 
                <xsl:choose>
                    <xsl:when test="author/gender = 'f'">της</xsl:when>
                    <xsl:otherwise>του</xsl:otherwise>
                </xsl:choose>
                <xsl:text> </xsl:text>
                <xsl:value-of select="author/name" />
            </h2>
            <div class="itemstream">
                <ul>
                    <xsl:for-each select="photo|poll|journal">
                        <li>
                        <xsl:attribute name="id"><xsl:value-of select="name()" />_<xsl:value-of select="@id" /></xsl:attribute>
                            <xsl:if test="name() = 'photo'">
                                <img>
                                    <xsl:attribute name="src"><xsl:value-of select="media/@url" /></xsl:attribute>
                                </img>
                            </xsl:if>
                            <h3>
                                <a>
                                    <xsl:attribute name="href">
                                        <xsl:if test="name() = 'photo'">photos/</xsl:if>
                                        <xsl:if test="name() = 'journal'">journals/</xsl:if>
                                        <xsl:if test="name() = 'poll'">polls/</xsl:if>
                                        <xsl:value-of select="@id" />
                                    </xsl:attribute>
                                    <xsl:value-of select="title" />
                                </a>
                                <xsl:if test="/social/@for = /social/favourites/author/name">
                                    <span class="icon deleteicon">&#215;</span>
                                </xsl:if>
                            </h3>
                            <xsl:if test="name() = 'poll'">
                            </xsl:if>
                            <xsl:if test="name() = 'journal'">
                                <span class="preview"><xsl:value-of select="text" /></span>
                            </xsl:if>
                            <div class="eof"></div>
                        </li>
                    </xsl:for-each>
                </ul>
            </div>
        </div>
    </xsl:for-each>
</xsl:template>

<xsl:template name="favourite.list">
    <xsl:if test="favourites or not(/social/@for = author/name)">
        <div class="note">
            <xsl:for-each select="favourites/user">
                <div class="love">
                &#9829; <span class="username"><a>
                <xsl:attribute name="href">users/<xsl:value-of select="name" /></xsl:attribute>
                <xsl:value-of select="name" /></a> </span>
                </div>
                <xsl:text> </xsl:text>
            </xsl:for-each>
            <a class="love linkbutton" href="" style="display:none">
                <xsl:attribute name="id">love_<xsl:value-of select="/social/*/@id" /></xsl:attribute>
                <strong>&#9829;</strong> Το αγαπώ!
            </a>
        </div>
    </xsl:if>
</xsl:template>
