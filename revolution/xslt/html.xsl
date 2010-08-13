<xsl:variable name="mastertemplate">
    <xsl:value-of select="/*[1]/@resource" />.<xsl:value-of select="/*[1]/@method" />
</xsl:variable>
<xsl:variable name="resource">
    <xsl:value-of select="/*[1]/@resource" />
</xsl:variable>
<xsl:variable name="method">
    <xsl:value-of select="/*[1]/@method" />
</xsl:variable>

<xsl:variable name="user" select="/*[1]/@for" />

<xsl:template match="/" priority="1">
    <xsl:choose>
        <!-- tiny master templates -->
        <xsl:when test="$resource = 'session'"><xsl:apply-templates /></xsl:when>
        
        <!-- full master templates -->
        <xsl:otherwise>
            <xsl:call-template name="html" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="title">
    <xsl:choose>
        <xsl:when test="/social/@resource = 'user' and /social/@method = 'view'">
            <xsl:value-of select="/social/user/name" />
        </xsl:when>
        <xsl:when test="/social/@resource = 'photo' and /social/@method = 'view'">
            <xsl:choose>
                <xsl:when test="/social/photo/title">
                    <xsl:value-of select="/social/photo/title" />
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="/social/photo/author/name" />
                </xsl:otherwise>
            </xsl:choose>
            <xsl:text> ● zino</xsl:text>
        </xsl:when>
        <xsl:when test="/social/@resource = 'poll' and /social/@method = 'view'">
            <xsl:value-of select="/social/poll/title" /> ●
            <xsl:value-of select="/social/poll/author/name" /> ● zino
        </xsl:when>
        <xsl:when test="/social/@resource = 'journal' and /social/@method = 'view'">
            <xsl:value-of select="/social/journal/title" />
            <xsl:text> ● </xsl:text>
            <xsl:value-of select="/social/journal/author/name" />
            <xsl:text> ● zino</xsl:text>
        </xsl:when>
        <xsl:when test="/social/@resource = 'news' and /social/@method = 'list'">
            Νέα ● zino
        </xsl:when>
        <xsl:when test="/social/@resource = 'journal' and /social/@method = 'list'">
            Ημερολόγια 
            <xsl:if test="/social/journals/author">
                <xsl:text> ● </xsl:text><xsl:value-of select="/social/journals/author/name" />
            </xsl:if>
            <xsl:text> ● zino</xsl:text>
        </xsl:when>
        <xsl:when test="/social/@resource = 'poll' and /social/@method = 'list'">
            Δημοσκοπήσεις
            <xsl:if test="/social/journals/author">
                <xsl:text> ● </xsl:text><xsl:value-of select="/social/journals/author/name" />
            </xsl:if> ● zino
        </xsl:when>
        <xsl:when test="/social/@resource = 'photo' and /social/@method = 'list'">
            <xsl:if test="/social/photos/author">
                <xsl:value-of select="/social/photos/author/name" />
                <xsl:text> ●</xsl:text>
            </xsl:if>
            Εικόνες ● zino
        </xsl:when>
        <xsl:when test="/social/@resource = 'favourite' and /social/@method = 'list'">
            <xsl:value-of select="/social/photos/author/name" />
            <xsl:text> ●</xsl:text>
            Αγαπημένα ● zino
        </xsl:when>
        <xsl:otherwise>
            zino
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!--eat all other output-->
<xsl:template match="*|text()" priority="-1"/>

<xsl:template name="html">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="el" lang="el">
        <xsl:attribute name="id"><xsl:value-of select="/social/@resource" />-<xsl:value-of select="/social/@method" /></xsl:attribute>
        <head>
            <base><xsl:attribute name="href"><xsl:value-of select="/social[1]/@generator" />/</xsl:attribute></base>
            <title>Zino</title>
            <link type="text/css" href="global.css" rel="stylesheet" />
            <link type="text/css" href="http://static.zino.gr/css/emoticons.css" rel="stylesheet" />
            <link type="text/css" href="http://static.zino.gr/css/spriting/sprite1.css" rel="stylesheet" />
            <meta http-equiv="X-UA-Compatible" content="IE=8,chrome=1" />
            <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
			<script type="text/javascript" src="global.js"></script>
            <script type="text/javascript">
                <xsl:if test="/social/@for">
                    var User = '<xsl:value-of select="/social/@for" />';
                </xsl:if>
                var Now = '<xsl:value-of select="/social/@generated" />';
                
                var XMLData = {
                    author: '<xsl:value-of select="/social/*/author/name" />'
                }
            </script>
        </head>
        <body onload="Comet.OnBodyLoaded()">
            <div id="world">
                <xsl:call-template name="banner" />
                <div id="content">
                    <xsl:apply-templates />
                </div>
            </div>
            <script type="text/javascript">
                $.ajaxSetup( {
                    dataType: 'xml'
                } );
                _aXSLT.defaultStylesheet = 'global.xsl';
                if ( window.ActiveXObject ) {
                    _aXSLT.ROOT_PATH = '*[1]';
                }

                $( function() { $( '.time' ).each( function () {
                    this.innerHTML = greekDateDiff( dateDiff( this.innerHTML, Now ) );
                    $( this ).addClass( 'processedtime' );
                } ); } );

                var Routing = {
                    'photo.view': PhotoView,
                    'photo.listing': PhotoListing,
                    'news.listing': News,
                    'poll.view': Poll,
                    'journal.view': Journal,
                    'user.view': Profile,
                    'favourite.listing': Favourite,
                    'friendship.listing': Friends,
                };
                var MasterTemplate = '<xsl:value-of select="$mastertemplate" />';
                if ( typeof Routing[ MasterTemplate ] != 'undefined' ) {
                    Routing[ MasterTemplate ].Init();
                }
                Notifications.Check();
                Presence.Init();
                Chat.Init();
            </script>
        </body>
    </html>
</xsl:template> 
