function innerxml( node ) {
    var text = node.xml || ( new XMLSerializer() ).serializeToString( node ) || "";
    var regex = new RegExp( "(^<\\w*" + node.tagName + "[^>]*>)|(<\\w*\\/\\w*" + node.tagName + "[^>]*>$)", "gi" );

    return text.replace( regex, "" );
}

var ExcaliburSettings = {
    Production: true
};

var Chat = {
     Visible: false,
     Loaded: false,
     ChannelsLoaded: {},
     ChannelByUserId: {},
     CurrentChannel: 0,
     Loading: false,
     UserId: 0,
     Authtoken: '',
     PreviousPageSelected: 0,
     FlashingTabs: 0,
     Timestamps: {
         Init: function( chatid ) {
             var items = $( chatid ).find( '.when' ).reverse();
             $( items ).each( function() {
                if ( !$( items ).filter( '.when.visible' ).length ) {
                    $( this ).show().addClass( 'visible' );
                    return true;
                }
                if ( $( this ).children( '.timestamp' ).text() < items.filter( '.when.visible:last' ).find( '.timestamp' ).text() - 5 * 60 * 1000
                    && $( this ).children( '.friendly' ).text() != items.filter( '.when.visible:last' ).find( '.friendly' ).text() ){ 
                    $( this ).show().addClass( 'visible' );
                }
            } );
         },
         Add: function( item ){
             var items = $( item ).closest( '.chatchannel' ).find( '.when.visible' );
             if( !items.length ){
                 $( item ).show().addClass( 'visible' );
                 return true;
             }
             if( item.children( '.timestamp' ).text() - 5 * 60 * 1000 > items.filter( ':last' ).find( '.timestamp' ).text()
                 && item.children( '.friendly' ).text() != items.filter( ':last' ).find( '.friendly' ).text() ) {
                 item.show().addClass( 'visible' );
             }

         }
     },
     NameClick: function () {
         var userid = this.id.split( 'u' )[ 1 ];
         if ( userid == Chat.UserId ) {
             return;
         }
         $( '#onlineusers li' ).removeClass( 'selected' );
         $( this ).addClass( 'selected' );
         Chat.Unflash( this.id.substr( 1 ) );
         if ( userid === 0 ) {
             Chat.Show( 0 );
         }
         else {
             Chat.ShowPrivate( userid );
         }
     },
     GetOnline: function () {
        $( '#onlineusers' ).css( { opacity: 0.5 } );
        $.get( 'users/online', {}, function ( res ) {
            var users = $( res ).find( 'user' );
            var user;
            var online = $( '#onlineusers' );
            var name;

            online.css( { opacity: 1 } );
            online = online[ 0 ];
            for ( i = 0; i < users.length; ++i ) {
                user = users[ i ];
                name = $( user ).find( 'name' ).text();
                Chat.OnUserOnline( $( user ).attr( 'id' ), name );
            }
            $( '#onlineusers li' ).click( Chat.NameClick );
        }, 'xml' );
     },
     HistoryFromXML: function ( res ) {
        var channelid = $( res ).find( 'chatchannel' ).attr( 'id' );
        if ( $( '#chatmessages_' + channelid ).length === 0 ) {
            Chat.CreateChannelHTML( channelid );
        }
        var history = $( '#chatmessages_' + channelid + ' ol' )[ 0 ];
        var messages = $( res ).find( 'discussion comment' );
        var text;
        var html = '', li;
        var shoutid;
        
        for ( var i = 0; i < messages.length; ++i ) {
            text = innerxml( $( messages[ i ] ).find( 'text' )[ 0 ] );
            author = $( messages[ i ] ).find( 'author name' ).text();
            shoutid = $( messages[ i ] ).attr( 'id' );

            li = '';
            li += '<li id="' + shoutid + '"><span class="when time">' + $( messages[ i ] ).find( 'date' ).text()  + '</span><strong';
            if ( author == User ) {
                li += ' class="self"';
            }
            li += '><a href="users/' + author + '">' + author + '</a>';
            text = Chat.GetFormattedText( text, author == User );
            if ( text == null ) {
                continue;
            }
            li += '</strong> ' + text + '</li>';
            html += li;
        }
        history.innerHTML = html;
        $( '.when:not(.processedtime)' ).load();
        Chat.Timestamps.Init( '#chatmessages_' + channelid );
    },
    GetMessages: function ( channelid, callback ) {
        $.get( 'chat/messages', { channelid: channelid }, function ( res ) {
            Chat.HistoryFromXML( res );
            callback( res );
        }, 'xml' );
    },
    LoadHistory: function ( channelid, callback ) {
         Chat.GetMessages( channelid, callback );
    },
    Narrator: {
        Say: function ( HTML ) {
             var li;

             if ( typeof HTML == 'string' ) {
                 li = document.createElement( 'li' );
                 li.innerHTML = HTML;
             }
             else {
                 li = HTML;
             }
             li.className = 'narrator';
             $( '#chatmessages_0 ol' )[ 0 ].appendChild( li );
             if ( Chat.AtEnd() && Chat.CurrentChannel == 0 ) {
                 li.scrollIntoView();
             }
        },
        OnPhotoUploaded: function ( res ) {
             var HTML;

             if ( $( res ).find( 'gender' ).length && $( res ).find( 'gender' ).text() == 'f' ) {
                 HTML = 'Η ';
             }
             else {
                 HTML = 'Ο ';
             }
             HTML += $( res ).find( 'author name' ).text() + ' ανέβασε ';
             HTML += '<a href="photos/' + $( res ).find( 'photo' ).attr( 'id' ) + '">μια φωτογραφία</a>';
             Chat.Narrator.Say( HTML );
        },
        OnJournalCreated: function ( res ) {
             var text, a, li;

             li = document.createElement( 'li' );
             if ( $( res ).find( 'gender' ).length && $( res ).find( 'gender' ).text() == 'f' ) {
                 text = 'Η ';
             }
             else {
                 text = 'Ο ';
             }
             text += $( res ).find( 'author name' ).text() + ' έγραψε το ημερολόγιο ';
             li.appendChild( document.createTextNode( text ) );
             a = document.createElement( 'a' );
             a.href = 'journals/' + $( res ).find( 'journal' ).attr( 'id' );
             a.appendChild( document.createTextNode( $( res ).find( 'journal title' ).text() ) );
             li.appendChild( a );
             Chat.Narrator.Say( li );
        },
        OnPollCreated: function ( res ) {
             var text, a, li;

             li = document.createElement( 'li' );
             if ( $( res ).find( 'gender' ).length && $( res ).find( 'gender' ).text() == 'f' ) {
                 text = 'Η ';
             }
             else {
                 text = 'Ο ';
             }
             text += $( res ).find( 'author name' ).text() + ' ρωτάει ';
             li.appendChild( document.createTextNode( text ) );
             a = document.createElement( 'a' );
             a.href = 'polls/' + $( res ).find( 'poll' ).attr( 'id' );
             a.appendChild( document.createTextNode( $( res ).find( 'poll title' ).text() ) );
             li.appendChild( a );
             Chat.Narrator.Say( li );
        }
    },
    Load: function () {
         Chat.Join( '0' ); // listen for global chat messages too
         Comet.Subscribe( 'presence', Chat.OnPresenceChange ); // listen for presence changes
         Comet.Subscribe( 'photo/list', Chat.Narrator.OnPhotoUploaded );
         Comet.Subscribe( 'poll/list', Chat.Narrator.OnPollCreated );
         Comet.Subscribe( 'journal/list', Chat.Narrator.OnJournalCreated );
         $( '#onlineusers li' ).click( Chat.NameClick );
         Kamibu.ClickableTextbox( $( '#chat .search input' )[ 0 ], 'Αναζήτηση', 'black', '#aaa' );
         if ( typeof User == 'undefined' ) {
             Kamibu.Go( 'login' );
             return false;
         }
         Chat.Show( 0 );
         $( '#chat textarea' ).keydown( function ( e ) {
             switch ( e.keyCode ) {
                case 27: // ESC
                    this.value = '';
                    $( this ).blur();
                    break;
                case 13: // enter
                    Chat.SendMessage( Chat.CurrentChannel, this.value );
                    this.value = '';
                    $( this ).blur();
                    $( this ).focus();
             }
         } ).keyup( function ( e ) {
             if ( e.keyCode == 13 ) { // enter
                this.value = '';
             }
         } );
         Chat.Typing.Init();
         Kamibu.ClickableTextbox( $( '#chat textarea' )[ 0 ], 'Γράψε ένα μήνυμα', 'black', '#ccc' );
         $( '.when.visible' ).live( 'updated', function(){
            if( $( this ).children( '.friendly' ).text() == $( this ).closest( 'li' ).prevAll( ':has(.when.visible):first' ).find( '.when .friendly' ).text() ){
                $( this ).hide().removeClass( 'visible' );
            }
         });
         Chat.Search.Init();
         Chat.Loaded = true;
         return true;
     },
     Sound: {
         Ready: false,
         Loading: false,
         Ding: function () {
             if ( !Chat.Sound.Ready && !Chat.Sound.Loading ) {
                 Chat.Sound.Loading = true;
                 $( '#jquery_jplayer' ).jPlayer( {
                     ready: function () {
                         this.element.jPlayer(
                            "setFile",
                            "http://static.zino.gr/revolution/sound/glass.mp3",
                            "http://static.zino.gr/revolution/sound/glass.ogg",
                            "http://static.zino.gr/revolution/sound/glass.wav"
                         );
                         Chat.Sound.Ready = true;
                         Chat.Sound.Loading = false;
                         Chat.Sound.Ding();
                     },
                     swfPath: '/js/jquery',
                     nativeSupport: true,
                     volume: 50
                 } );
                 return;
             }
             $( '#jquery_jplayer' ).jPlayer( 'play' );
         }
     },
     BindClick: function(){
        $( '#chatbutton' ).click( function () {
            if ( Chat.UserId == 0 ) { // session request hasn't yet finished
                                      // wait for it... dary.
                 Chat.Visible = true; // must become visible asap
            }
            else {
                 Chat.Toggle();
            }
            return false;
        } );
     },
     Init: function () {
         // keep this function short
         // these things run on every page load
         // if you just want some initialization operation that only needs to
         // run when the chat is opened
         // use the Chat.Load() function, not this.
         // Chat.Load() is only called once.
        Chat.BindClick();
        document.domain = 'zino.gr';
        $.get( 'session', function ( res ) {
            Chat.UserId = $( res ).find( 'user' ).attr( 'id' );
            Chat.Authtoken = $( res ).find( 'authtoken' ).text();
            Comet.Init();
            Chat.Join( Chat.UserId + ':' + Chat.Authtoken ); // listen for private messages only
            $( document.body ).append(
                 '<div style="display:none" id="chat">'
                     + '<div class="userlist">'
                         + '<div class="search"><input type="text" value="Αναζήτηση"></div>'
                         + '<ol id="onlineusers"><li class="selected world" id="u0">Zino</li></ol>'
                         + '<ol id="searchlist"></ol>'
                     + '</div>'
                     + '<div class="textmessages">'
                         + '<div class="loading" style="display:none">Λίγα δευτερόλεπτα υπομονή...</div>'
                         + '<div id="chatmessages"></div>'
                         + '<div id="outgoing"><div><textarea style="color:#ccc">Στείλε ένα μήνυμα</textarea></div></div>'
                     + '</div>'
                 + '</div><div id="jquery_jplayer"></div>' );
            if ( Chat.Visible ) { // user has already clicked the chat button
                Chat.Visible = false;
                Chat.Toggle();
            }
        } );
     },
     Search: {
        key: '',
        Cancel: function(){
            $( '#searchlist' ).empty().hide();
            $( '#onlineusers' ).show();
            Chat.Search.key = '';
        },
        Typing: function( text ){
            if( text.length <= 1 ){
                Chat.Search.Cancel();
                return;
            }

            if( Chat.Search.key == '' || text.substr( 0, Chat.Search.key.length ) != Chat.Search.key ){ //the key is not valid
                Chat.Search.GetUsers( text );
                return;
            }
            Chat.Search.Filter( text );
        },
        GetUsers: function( text ){
            Chat.Search.key = text;
            $( '#searchlist' ).empty().append( '<li class="user_loading"><img src="http://static.zino.gr/revolution/loading.gif" /></li>' );
            $( '#searchlist' ).show();
            $( '#onlineusers' ).hide();
            $.get( 'users/search', { query: text }, function( data ){
                $( '#searchlist' ).empty();
                $( data ).find( 'user' ).each( function(){
                    if( $( this ).children( 'name' ).text() == User ){
                        return;
                    }
                    var li = $( '<li id="searchuser_' + $( this ).attr( 'id' ) + '">' +
                                    '<span class="user">' + $( this ).children( 'name' ).text() + '</span>' +
                                '</li>' );
                    if( !$( '#u' + $( this ).attr( 'id' ) ).length ){
                        li.addClass( 'offline' );
                    }
                    li.appendTo( '#searchlist' );
                });
                Chat.Search.Filter();
            });
        },
        Filter: function(){
            var key = $( '.search input' ).val();
            if( key == '' ){
                Chat.Search.Cancel();
                return;
            }
            if( $( '#searchlist li.user_loading' ).length ){
                return;
            }
            $('#searchlist li' ).hide().filter( ':containsCI(' + key + ')' ).show().each( function(){
                var oldtext = $( this ).text();
                var newtext = '<span class="sel">' +
                                oldtext.substr( 0, key.length ) +
                             '</span>' +
                             oldtext.substr( key.length );
                $( this ).children( 'span.user' ).html( newtext );
            });

        },
        Init: function(){
            $( '.search' ).show();
            $( '.search input' ).keydown( function( e ){
                if( e.which == 27 ){
                    Chat.Search.Cancel();
                    $( '.search input' ).val( '' ).blur();
                }
            }).keyup( function(){
                Chat.Search.Typing( $( this ).val() );
            });
            $( '#searchlist li:not(.user_loading)' ).live( 'click', function(){
                var id = $( this ).attr( 'id' ).split( '_' )[ 1 ];
                Chat.ShowPrivate( id );
                Chat.OnUserOnline( id, $( this ).text() );
                $( '#onlineusers li' ).removeClass( 'selected' );
                if( $( this ).hasClass( 'offline' ) ){
                    $( '#u' + id ).addClass( 'offline' );
                }
                $( '#u' + id ).addClass( 'selected' );
                $( this ).addClass( 'selected' );
            });
        }
     },
     SendMessage: function ( channelid, text ) {
         if ( text.replace( /^\s+/, '' ).replace( /\s+$/, '' ).length === 0 ) {
             // empty message
             return;
         }

         var li = document.createElement( 'li' );
         li.innerHTML = '<strong class="self"><a></a></strong> <span class="text"></span>';
         $( 'a', li ).attr( 'href', 'users/' + User ).text( User ).end()
         $( 'span.text', li ).text( text );

         $( '#chatmessages_' + channelid + ' ol' )[ 0 ].appendChild( li );
         $( '#chatmessages_' + channelid + ' ol' )[ 0 ].lastChild.scrollIntoView();
         Chat.Typing.Update( channelid );
         var lastChild = $( '#chatmessages_' + channelid + ' ol' )[ 0 ].lastChild;

         $.post( 'chat/message/create', {
            channelid: channelid,
            text: text
         }, function ( res ) {
             var shoutid = $( res ).find( 'comment' ).attr( 'id' );
                
            if ( document.getElementById( shoutid ) ) {
                // already received this message through comet
                $( lastChild ).remove(); // remove duplicate
            }
            // didn't receive it through comet yet; update the innerHTML and ids
            // when it's received through comet, it'll be ignored
            $( lastChild ).find( 'span.text' )[ 0 ].innerHTML = innerxml( $( res ).find( 'text' )[ 0 ] );
            $( lastChild )[ 0 ].id = shoutid;
            $( lastChild ).prepend( '<span class="when time">' + $( res ).find( 'date' ).text()  + '</span>' ).children( '.when' ).load();
            Chat.Timestamps.Add( $( lastChild ).find( '.when' ) );
         }, 'xml' );
     },
     AtEnd: function () {
         var container = $( '#chatmessages_' + Chat.CurrentChannel + ' .scrollcontainer' )[ 0 ];
         var history = $( '#chatmessages_' + Chat.CurrentChannel + ' ol' )[ 0 ];
         var EPSILON = 200;

         return container.offsetHeight + container.scrollTop > history.offsetHeight - EPSILON;
     },
     GetFormattedText: function ( text, mine ) {
         if ( text.substr( 0, '/__zino:'.length ) == '/__zino:' ) { // action
             var parts = text.split( ':' );
             if ( parts.length < 2 ) {
                 return null;
             }
             switch ( parts[ 1 ] ) {
                 case 'file':
                    if ( parts[ 2 ].substr( 0, 1 ) == '<' ) { // <img> ...
                        if ( mine ) {
                            return '<span class="text action"><div>Έστειλες ένα αρχείο.</div>' + parts.splice( 2, parts.length - 2 ).join( ':' ) + '</span>';
                        }
                        return '<span class="text action"><div>Έλαβες ένα αρχείο.</div>' + parts.splice( 2, parts.length - 2 ).join( ':' ) + '<div>Κάνε δεξί κλικ και αποθήκευση για να το κατεβάσεις.</div></span>';
                    }
                    if ( mine ) {
                        return '<span class="text action">Έστειλες <a href="' + parts[ 2 ] + '" target="_blank">ένα αρχείο</a></span>';
                    }
                    return '<span class="text action">Έλαβες ένα αρχείο. <a href="' + parts[ 2 ] + '" target="_blank">Λήψη τώρα</a></span>';
                 default:
                    return null;
             }
         }
         return '<span class="text">' + text + '</span>';
     },
     OnMessageArrival: function ( res ) {
         var channelid = $( res ).find( 'chatchannel' ).attr( 'id' );
         Chat.CreateChannelHTML( channelid );
         var history = $( '#chatmessages_' + channelid + ' ol' )[ 0 ];
         var messages = $( res ).find( 'discussion comment' );
         var text, newmessage = false;
         var html = '';
         var li, shoutid, author;
         var container = $( '#chatmessages_' + channelid + ' div.scrollcontainer' )[ 0 ];

         for ( var i = 0; i < messages.length; ++i ) {
             shoutid = $( messages[ i ] ).attr( 'id' );
             author = $( messages[ i ] ).find( 'author name' ).text();
             if ( document.getElementById( shoutid ) ) {
                 // message has already been received
                 continue;
             }
             if ( author == User ) {
                 continue; // don't display my own messages; they've already been added by the SendMessage function
             }
             newmessage = true;
             text = innerxml( $( messages[ i ] ).find( 'text' )[ 0 ] );
             text = Chat.GetFormattedText( text, false );
             if ( text == null ) {
                 return;
             }
             li = document.createElement( 'li' );
             li.id = shoutid;
             li.innerHTML = '<span class="when time">' + $( messages[ i ] ).find( 'date' ).text()  + '</span><strong><a href="users/' + author + '">' + author + '</a></strong> ' + text + '</li>'; 
             history.appendChild( li );
             Chat.Typing.OnStop( author );
             Chat.Timestamps.Add( $( '.time:not(.processedtime)' ).load() );
         }
         if ( typeof text == 'undefined' ) {
             // no need to handle any messages
             return;
         }
         if ( Chat.CurrentChannel == channelid ) {
             if ( typeof li != 'undefined' ) {
                 if ( Chat.AtEnd() ) { // if the user has already scrolled to the end show new message
                     li.scrollIntoView();
                 } // else, don't scroll them down if they're browsing the history
             }
         }
         else {
             var userid, cid, found, username;

             found = false;
             for ( userid in Chat.ChannelByUserId ) {
                 cid = Chat.ChannelByUserId[ userid ];
                 if ( cid == channelid && channelid != 0 ) {
                     found = true;
                     Chat.Flash( userid, text );
                     if ( $( '#u' + userid ).hasClass( 'flash' ) ) {
                         username = $( '#u' + userid ).find( 'span.username' ).text();
                     }
                     else {
                         username = $( '#u' + userid ).text();
                     }
                     Chat.PopBubble( userid, username, text, channelid );
                 }
             }
             if ( !found && newmessage ) {
                 $.get( 'chat/' + channelid, {}, function ( res ) { 
                     var users = $( res ).find( 'user' );
                     for ( var i = 0; i < users.length; ++i ) {
                         userid = $( users[ i ] ).attr( 'id' );
                         username = $( users[ i ] ).find( 'name' ).text();
                         if ( userid != Chat.UserId ) {
                             Chat.ChannelByUserId[ userid ] = channelid;
                             if ( !$( '#u' + userid ).length ) {
                                 Chat.OnUserOnline( userid, username );
                             }
                             Chat.Flash( userid, text );
                             break;
                         }
                     }
                     Chat.PopBubble( userid, username, text, channelid );
                 } );
             }
         }
     },
     PopBubble: function ( userid, username, text, channelid ) {
         if ( Chat.Visible ) {
             return;
         }
         if ( !$( '#chatbubbles' ).length ) {
             $( document.body ).append( '<div id="chatbubbles"></div>' );
         }
         if ( !$( '#popbubble_' + userid ).length ) {
             $( '#chatbubbles' ).append( '<div class="chatbubble" id="popbubble_' + userid + '"><img src="" alt="' + username + '" /><div class="text"><span><strong>' + username + '</strong> λέει:</span></div></div>' );
             $( '#popbubble_' + userid + ' .text' )[ 0 ].innerHTML += text;
             $( '#popbubble_' + userid ).click( function () {
                 Notifications.Hide();
                 Chat.Toggle();
                 Chat.Show( channelid );
                 $( '#popbubble_' + userid ).remove();
             } );
             $.get( 'users/' + username, { verbose: 0 }, function ( res ) {
                 if ( $( res ).find( 'avatar' ) ) {
                     $( '#popbubble_' + userid + ' img' )[ 0 ].src = $( res ).find( 'media' ).attr( 'url' );
                 }
                 else {
                     $( '#popbubble_' + userid + ' img' )[ 0 ].src = 'http://static.zino.gr/phoenix/anonymous100.jpg'; 
                 }
             } );
             var pos = 0;

             ( function () {
                 pos += 0.1;
                 if ( pos > Math.PI ) {
                     pos -= Math.PI;
                 }
                 if ( $( '#popbubble_' + userid ).length ) {
                     $( '#popbubble_' + userid ).css( { opacity: 0.5 + 0.5 * Math.sin( pos ) } );
                     setTimeout( arguments.callee, 50 );
                 }
             } )();
             Chat.Sound.Ding();
         }
     },
     OnUserOnline: function ( userid, username ) {
         var lis = $( '#onlineusers li' );
         var li;
         var compare;

         if ( typeof User != 'undefined' && username == User ) {
             return;
         }
         
         if ( $( '#u' + userid ).length ) {
             $( '#u' + userid ).removeClass( 'offline' );
             return;
         }
         var origname = username;
         username = username.toLowerCase();

         for ( var i = 1; i < lis.length; ++i ) {
             li = lis[ i ];
             if ( $( li ).hasClass( 'flash' ) ) {
                 // username of person to compare with 
                 compare = $( li ).find( 'span.username' ).text();
             }
             else {
                 compare = $( li ).text();
             }
             if ( username < compare.toLowerCase() ) {
                 break;
             }
         }
         var newuser = document.createElement( 'li' );
         $( newuser ).html( '<span class="user"></span>' ).children( 'span' ).text( origname );
         newuser.id = 'u' + userid;
         newuser.onclick = Chat.NameClick;

         if ( i == lis.length ) {
             $( '#onlineusers' ).append( newuser );
         }
         else {
             $( '#onlineusers' )[ 0 ].insertBefore( newuser, lis[ i ] );
         }
     },
     OnUserOffline: function ( userid, username ) {
         if ( $( '#u' + userid ).hasClass( 'flash' ) ) {
             // do not remove someone who is talking to you
             $( '#u' + userid ).addClass( 'offline' );
             return;
         }
         $( '#u' + userid ).remove();
         Chat.Typing.OnStop( username );
     },
     FlashingTitleTimeout: null,
     FlashTitle: function () {
         var toggle = -1;

         clearInterval( Chat.FlashingTitleTimeout );
         if ( Chat.FlashingTabs ) {
             document.title = 'Σου μιλάνε!';
             Chat.FlashingTitleTimeout = setInterval( function() { 
                 ++toggle;
                 switch ( toggle ) { 
                     case 0:
                        document.title = 'Chat στο zino';
                        break;
                     case 1:
                        var $talkingheads = $( '.userlist .flash' );
                        document.title = $( $talkingheads[ Math.floor( Math.random() * $talkingheads.length ) ] ).find( '.username' ).text();
                        break;
                     case 2:
                        document.title = 'Σου μιλάνε!';
                        toggle = -1;
                        break;
                }
             }, 1000 );
         }
         else {
             document.title = 'Chat στο zino';
         }
     },
     Flash: function ( userid, message ) {
         if ( Chat.Visible ) {
             $( '#u' + userid )[ 0 ].scrollIntoView();
         }
         if ( $( '#u' + userid ).hasClass( 'flash' ) ) {
             $( '#u' + userid + ' .text' ).html( message );
             return;
         }
         ++Chat.FlashingTabs;
         Chat.FlashTitle();
         Chat.Sound.Ding();
         $( '#u' + userid ).addClass( 'flash' ).html(
            '<span class="username">' + $( '#u' + userid ).text() + '</span>'
            + '<span class="text">' + message + '</span>'
         );
     },
     Unflash: function ( userid ) {
         if ( !$( '#u' + userid ).hasClass( 'flash' ) ) {
             return;
         }
         --Chat.FlashingTabs;
         Chat.FlashTitle();
         $( '#u' + userid ).removeClass( 'flash' );
         var uname = $( '#u' + userid + ' .username' ).text();
         $( '#u' + userid ).html( '<span class="user"></span>' ).children().text( uname );
     },
     Join: function ( channelid ) {
         // Listen to push messages here
         Comet.Subscribe( 'chat/messages/list/' + channelid, Chat.OnMessageArrival );
         Comet.Subscribe( 'chat/typing/list/' + channelid, Chat.Typing.OnStateChange );
     },
     Typing: {
         People: {}, // channelid => [ username1, username2, ... ]
         Sent: false, // whether we've sent that we're currently typing; don't resend too often to avoid excessive network traffic
         StopTimeout: 0, // the timeout object before we sent the event that we've stopped typing
         ResendTimeout: 0, // the timeout object before we know we must send again the fact that we're typing
         OnStateChange: function ( res ) {
             var channelid = $( res ).find( 'chatchannel' ).attr( 'id' );
             var username = $( res ).find( 'chatchannel user name' ).text();
             var typing = $( res ).find( 'chatchannel user' ).attr( 'typing' ) == '1';

             if ( username != User ) {
                 if ( typing ) {
                     Chat.Typing.OnStart( channelid, username );
                 }
                 else {
                     Chat.Typing.OnStop( username );
                 }
             }
         },
         OnStart: function ( channelid, username ) {
             if ( typeof Chat.Typing.People[ channelid ] == 'undefined' ) {
                 Chat.Typing.People[ channelid ] = {};
             }
             Chat.Typing.People[ channelid ][ username ] = true;
             Chat.Typing.Update( channelid );
         },
         OnStop: function ( username ) {
             var i;

             for ( i in Chat.Typing.People ) {
                 if ( typeof Chat.Typing.People[ i ][ username ] != 'undefined' ) {
                     delete Chat.Typing.People[ i ][ username ];
                     Chat.Typing.Update( i );
                 }
             }
         },
         Update: function ( channelid ) {
             var typingHTML = '';
             var typists = [];
             var i;

             for ( i in Chat.Typing.People[ channelid ] ) {
                 typists.push( i );
             }

             if ( typists.length > 0 ) {
                 if ( typists.length == 1 ) {
                     typingHTML = typists[ 0 ] + ' πληκτρολογεί...';
                 }
                 else {
                     typingHTML = typists.join( ', ' ) + ' πληκτρολογούν...';
                 }
             }
                 
             if ( typingHTML !== '' ) {
                 $( '#chatmessages_' + channelid + ' p.typing' ).html( typingHTML );
                 $( '#chatmessages_' + channelid + ' p.typing' ).css( { display: 'block' } );
                 if ( Chat.AtEnd() ) {
                     $( '#chatmessages_' + channelid + ' .typing' )[ 0 ].scrollIntoView();
                 }
             }
             else {
                 $( '#chatmessages_' + channelid + ' p.typing' ).css( { display: 'none' } );
             }
         },
         Init: function () {
             $( '#chat textarea' ).keypress( function ( e ) {
                 clearTimeout( Chat.Typing.StopTimeout ); // make sure we don't stop; we just started
                 Chat.Typing.StopTimeout = setTimeout( function () { // if user has not touched the keyboard for 4 seconds, show them as not typing explicitly
                    $.post( 'chat/typing', {
                        typing: 0
                    } );
                    Chat.Typing.Sent = false;
                    clearTimeout( Chat.Typing.Resendtimeout ); // we've already set Sent = false
                 }, 4000 );
                 if ( !Chat.Typing.Sent ) { // sent the fact that we are still typing every 10 seconds; just so that remote client doesn't "timeout" and thinks we've stopped
                    $.post( 'chat/typing', {
                        channelid: Chat.CurrentChannel,
                        typing: 1
                    } ); 
                    Chat.Typing.Sent = true;
                    Chat.Typing.ResendTimeout = setTimeout( function () { // after a while, know that we should send the typing event again
                        Chat.Typing.Sent = false;
                    }, 10000 );
                 }
             } );
         }
     },
     OnPresenceChange: function ( res ) {
         var method = $( res ).find( 'operation' ).attr( 'method' );
         if ( method == 'create' ) {
             Chat.OnUserOnline( $( res ).find( 'user' ).attr( 'id' ), $( res ).find( 'user name' ).text() );
         }
         else { // method == 'delete'
             Chat.OnUserOffline( $( res ).find( 'user' ).attr( 'id' ), $( res ).find( 'user name' ).text() );
         }
     },
     NowLoading: function () {
         document.body.style.cursor = 'wait';
         $( '.chatchannel' ).hide();
         $( '.textmessages .loading' ).show();
     },
     DoneLoading: function () {
         document.body.style.cursor = 'default';
         $( '.textmessages .loading' ).hide();
     },
     // switch to a channel given a userid; if not loaded, it will load it
     ShowPrivate: function ( userid ) {
         var channelid;
         if ( typeof Chat.ChannelByUserId[ userid ] == 'undefined' ) {
             Chat.NowLoading();
             $.get(
                'chat/messages', {
                    channelid: 0,
                    userid: userid
                },
                function ( res ) {
                    if( !$( '#u' + userid ).hasClass( 'selected' ) ){
                        return;
                    }
                    channelid = $( res ).find( 'chatchannel' ).attr( 'id' );
                    Chat.ChannelByUserId[ userid ] = channelid;
                    Chat.HistoryFromXML( res );
                    Chat.ChannelsLoaded[ channelid ] = true;
                    Chat.DisplayChannel( channelid );
                    Chat.DoneLoading();
                }, 'xml'
             );
         }
         else {
             channelid = Chat.ChannelByUserId[ userid ];
             Chat.DisplayChannel( channelid );
         }
     },
     // switches to given channel; loads it if not yet loaded
     Show: function ( channelid ) {
         if ( typeof Chat.ChannelsLoaded[ channelid ] == 'undefined' ) {
             Chat.NowLoading();
             Chat.LoadHistory( channelid, function () {
                 Chat.ChannelsLoaded[ channelid ] = true;
                 Chat.DisplayChannel( channelid );
                 Chat.DoneLoading();
             } );
         }
         else {
             Chat.DisplayChannel( channelid );
         }
     },
     File: {
         $modal: null,
         Send: function () {
             axslt( false, 'call:chat.modal.file', function() {
                 Chat.File.$modal = $( this ).filter( 'div' );
                 Chat.File.$modal.prependTo( 'body' ).modal();
             } );
             return false;
         },
         Hide: function () {
             Chat.File.$modal.jqmHide();
         },
         OnUploaded: function ( url ) {
             // alert( 'File uploaded: ' + url );
             Chat.File.Hide();
             channelid = Chat.CurrentChannel;
             $.post( 'chat/message/create', {
                channelid: channelid,
                text: '/__zino:file:' + url
             }, function ( res ) {
                 var li = document.createElement( 'li' );
                 li.innerHTML = '<strong class="self">&nbsp;</strong> <span class="text action"></span>';
                 $( li )
                    .children( 'span.text' ).html( '<a href="' + url + '" target="_blank">Το αρχείο σου</a> στάλθηκε επιτυχώς.' );
                 $( '#chatmessages_' + channelid + ' ol' )[ 0 ].appendChild( li );
                 $( '#chatmessages_' + channelid + ' ol' )[ 0 ].lastChild.scrollIntoView();
             } );
         }
     },
     CreateChannelHTML: function ( channelid ) {
         if ( $( '#chatmessages_' + channelid ).length === 0 ) {
             $( '#chatmessages' )[ 0 ].innerHTML += '<div class="chatchannel" id="chatmessages_' + channelid + '" style="display:none"><div class="scrollcontainer"><ol></ol><p class="typing"></p></div></div>';
             if ( channelid == 0 ) {
                 return;
             }
             // create the user pane
             var $chatmessages = $( '#chatmessages_' + channelid );
             var $panel = $( '<div>'
                            + '<div class="userinfo">'
                            + '  <ul class="toolbox"></ul>'
                            + '  <div><h3></h3><ul></ul></div>'
                            + '</div>'
                           + '</div>' );

             $chatmessages.find( '.scrollcontainer' ).css( { top: '50px' } );
             $chatmessages.prepend( $panel );
             $panel.find( '.toolbox' ).append( '<li><a class="sendfile" href=""><img src="http://static.zino.gr/revolution/page_white_get.png" alt="Αποστολή αρχείου" title="Αποστολή αρχείου" /></a></li>' );
             $chatmessages.find( 'a.sendfile' ).click( Chat.File.Send );
             $.get( 'chat/' + channelid, function ( res ) {
                 var users = $( res ).find( 'user' );
                 for ( var i = 0; i < users.length; ++i ) {
                     userid = $( users[ i ] ).attr( 'id' );
                     username = $( users[ i ] ).find( 'name' ).text();
                     if ( userid != Chat.UserId ) {
                         Chat.ChannelByUserId[ userid ] = channelid;
                         break;
                     }
                 }
                 $panel.find( 'h3' ).text( username );
                 $.get( 'users/' + username + '?verbose=2', function ( res ) {
                     var avatar = 'http://static.zino.gr/phoenix/anonymous100.jpg'; 
                     if ( $( res ).find( 'user avatar' ).length ) {
                         avatar = $( res ).find( 'user avatar media' ).attr( 'url' );
                     }
                     var img = '<a href="users/' + username + '"><img src="' + avatar + '" alt="' + username + '" title="Προβολή προφίλ" /></a>';
                     var lis = [];
                     if ( $( res ).find( 'gender' ).length ) {
                         if ( $( res ).find( 'gender' ).text() == 'f' ) {
                             lis.push( 'Κορίτσι' );
                         }
                         else {
                             lis.push( 'Αγόρι' );
                         }
                     }
                     if ( $( res ).find( 'age' ).length ) {
                         lis.push( $( res ).find( 'age' ).text() );
                     }
                     if ( $( res ).find( 'location' ).length ) {
                         lis.push( $( res ).find( 'location' ).text() );
                     }
                     var lihtml = '';
                     for ( var i = 0; i < lis.length; ++i ) {
                         if ( i == lis.length - 1 ) {
                             lihtml += '<li class="last">';
                         }
                         else {
                             lihtml += '<li>';
                         }
                         lihtml += lis[ i ] + '</li>';
                     }
                     $panel.find( '.userinfo' ).prepend( img );
                     $( $panel.find( 'ul' )[ 1 ] ).prepend( lihtml );
                 } );
             } );
         }
     },
     // switch to an already loaded channel
     DisplayChannel: function ( channelid, userid ) {
         $( '.chatchannel' ).hide();
         $( '#chatmessages_' + channelid ).show();
         if ( $(' #chatmessages_' + channelid + ' li' ).length ) {
             var messages = $( '#chatmessages_' + channelid + ' li' );
             messages[ messages.length - 1 ].scrollIntoView();
         }
         Chat.CurrentChannel = channelid;
        $( '#outgoing' ).find( 'textarea' ).focus();
     },
     // hide/show the chat application
     Toggle: function () {
         if ( !Chat.Loaded ) {
             if ( !Chat.Load() ) {
                 return;
             }
         }
         if ( Chat.Visible ) {
             document.title = Chat.OriginalTitle;
             $( '#chat' ).hide();
             $( '#content' ).show();
             if ( Chat.PreviousPageSelected != -1 ) {
                 $( $( 'div.bar ul li' )[ Chat.PreviousPageSelected ] ).addClass( 'selected' );
             }
             $( '#chatbutton' ).parent().removeClass( 'selected' );
         }
         else {
             Notifications.Hide();
             Chat.OriginalTitle = document.title;
             document.title = 'Chat στο zino';
             $( '#chat' ).show();
             $( '#content' ).hide();
             var menu = $( 'div.bar ul li' );
             Chat.PreviousPageSelected = -1;
             for ( var i = 0; i < menu.length; ++i ) {
                 if ( menu[ i ].className == 'selected' ) {
                     Chat.PreviousPageSelected = i;
                     $( menu[ i ] ).removeClass( 'selected' );
                     break;
                 }
             }
             $( '#chatbutton' ).parent().addClass( 'selected' );
             Chat.GetOnline();
             var ol = $( '#chatmessages_' + Chat.CurrentChannel + ' ol' )[ 0 ];
             if ( ol != null && ol.lastChild != null ) {
                 ol.lastChild.scrollIntoView();
             }
         }
         Chat.Visible = !Chat.Visible;
     }
};
