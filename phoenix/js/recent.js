var Recent = {
    Events: [],
    Loading: true,
    Now: 0,
    Interval: 20,
    Resolution: 1,
    Smoothness: 0.03,
    Speed: 1,
    Bubbles: [],
    OnLoad: function () {
        document.title = 'Φόρτωση...';
        document.title = 'OnLoad';
        setInterval( Recent.GetEvents, Recent.Interval * 1000 );
        setInterval( Recent.Animate, Recent.Smoothness * 1000 );
        Recent.GetEvents();
    },
    OnFirstDownload: function ( now ) {
        document.title = 'Πρόσφατα στο Zino';
        $( 'div#recentevents img.loader' ).remove();
        Recent.Now = now;
        setInterval( Recent.Process, Recent.Resolution * 1000 );
    },
    GetEvents: function () {
        document.title = 'GetEvents';
        Coala.Cold( 'recent/get', { f: Recent.GotEvents } );
    },
    GotEvents: function ( events, now ) {
        if ( Recent.Loading ) {
            Recent.Loading = false;
            Recent.OnFirstDownload( now );
        }
        if ( events.length ) {
            document.title = 'GotEvents: ' + events.length + ' ( ' + events[ 0 ].created + ' / ' + Recent.Now + ' )';
        }
        for ( i = 0; i < events.length; ++i ) {
            var event = events[ i ];
            if ( event.created < Recent.Now - Recent.Interval ) { // filter out too old events (older than 20 seconds ago) -- don't consider them at all
                continue;
            }
            Recent.Events.push( event );
        }
    },
    DisplayAvatar: function ( who ) {
        return '<div class="who">'
                    /* + '<a href="http://' 
                        + who.subdomain 
                        + '.zino.gr" target="_blank" title="Προβολή προφίλ '
                        + ( who.gender == 'f'? 'της ': 'του ' ) 
                        + who.name
                        + '">'
                        + '<img src="http://images.zino.gr/media/'
                        + who.id + '/' + who.avatar 
                        + '/' + who.avatar + '_100.jpg" alt="'
                        + who.name
                        + '" width="50" height"50" class="avatar" />'
                        + '<span class="nick">'
                        + who.name
                        + '</span>'
                    + '</a>'
                    + '<img src="speech.png" class="speech" />' */
                + '</div>';
    },
    DisplayEvent: function ( event ) {
        var div = document.createElement( 'div' );

        switch ( event.type ) {
            case 'Comment':
                div.innerHTML = 
                  Recent.DisplayAvatar( event.who )
                  + '<div class="what"><a href="' + event.url + '" target="_blank" title="Προβολή του σχόλιου">'
                  + event.text
                  + '</a></div>';
                break;
            case 'Favourite':
                div.innerHTML = 
                    Recent.DisplayAvatar( event.who ) 
                    + '<div class="what"><a href="' + event.url + '" target="_blank" title="Προβολή του στοιχείου"><em>Πρόσθεσε κάτι στα αγαπημένα</em></div>';
                break;
            case 'FriendRelation':
                div.innerHTML = 
                    Recent.DisplayAvatar( event.who ) 
                    + '<div class="what"><em>Πρόσθεσε ένα φίλο</em></div>';
                break;
            case 'Image':
                div.innerHTML = 
                    Recent.DisplayAvatar( event.who )
                    + '<div class="what"><em>Ανέβασε μία φωτογραφία</em></div>';
                break;
            case 'User':
                div.innerHTML = 
                    Recent.DisplayAvatar( event.who ) 
                    + '<div class="what"><em>Είναι καινούργιος στο Zino!</em></div>';
                break;
            case 'Poll':
                div.innerHTML = 
                    Recent.DisplayAvatar( event.who )
                    + '<div class="what"><em>Δημιούργησε μία δημοσκόπηση</em></div>';
                break;
            case 'Poll':
                div.innerHTML = 
                    Recent.DisplayAvatar( event.who ) 
                    + '<div class="what"><em>Έγραψε ημερολόγιο</em></div>';
                break;
            case 'ImageTag':
                div.innerHTML = 
                    Recent.DisplayAvatar( event.who ) 
                    + '<div class="what"><em>αναγνώρισε κάποιον σε μία φωτογραφία</em></div>';
                break;
        }
        Recent.PutBubble( div );
    },
    PutBubble: function ( div ) {
        var par = document.getElementById( 'recentevents' );
        div.className = 'event';
        par.appendChild( div );
        var item = {
            'node': div,
            'position': -div.scrollHeight,
            'speed': 1 + Math.random()
        };
        item.node.style.bottom = item.position + 'px';
        item.node.style.left = Math.round( Math.random() * ( document.body.scrollWidth - div.scrollWidth ) ) + 'px';
        Recent.Bubbles.push( item );
    },
    Animate: function () {
        for ( i = 0; i < Recent.Bubbles.length; ++i ) {
            Recent.Bubbles[ i ].position += Recent.Speed * Recent.Bubbles[ i ].speed;
            Recent.Bubbles[ i ].node.style.bottom = Recent.Bubbles[ i ].position + 'px';
        }
    },
    RemoveOldies: function () {
        var Keep = [];
        
        for ( i = 0; i < Recent.Bubbles.length; ++i ) {
            if ( Recent.Bubbles[ i ].position <= body.scrollHeight + Recent.Bubbles[ i ].node.scrollHeight ) {
                Keep.push( Recent.Bubbles[ i ] );
            }
        }
        Recent.Bubbles = Keep;
    },
    Process: function () {
        Recent.Now += Recent.Resolution;
        
        var newArray = [];
        
        for ( i = 0; i < Recent.Events.length; ++i ) {
            var event = Recent.Events[ i ];
            if ( event.created < Recent.Now - Recent.Interval ) { // display events with a 20-second offset from the time they ~really~ happened
                Recent.DisplayEvent( event );
            }
            else {
                newArray.push( event );
            }
        }
        
        Recent.Events = newArray;
    }
};
Recent.OnLoad();
