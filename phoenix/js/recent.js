var Recent = {
    Events: [],
    Loading: true,
    Now: 0,
    Interval: 20,
    Resolution: 1,
    OnLoad: function () {
        document.title = 'Φόρτωση...';
        document.title = 'OnLoad';
        setInterval( Recent.GetEvents, Recent.Interval * 1000 );
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
    GetName: function ( who ) {
        if ( 'f' == who.gender ) {
            return 'Η ' + who.name;
        }
        return 'Ο ' + who.name;
    },
    DisplayEvent: function ( event ) {
        document.title = 'DisplayEvent';
        var par = document.getElementById( 'recentevents' );
        var div = document.createElement( 'div' );

        switch ( event.type ) {
            case 'Comment':
                div.innerHTML = Recent.GetName( event.who ) + ' είπε: ' + event.text;
                break;
            case 'Favourite':
                div.innerHTML = Recent.GetName( event.who ) + ' πρόσθεσε κάτι στα αγαπημένα';
                break;
            case 'FriendRelation':
                div.innerHTML = Recent.GetName( event.who ) + ' πρόσθεσε ένα φίλο';
                break;
            case 'Image':
                div.innerHTML = Recent.GetName( event.who ) + ' ανέβασε μία φωτογραφία';
                break;
            case 'User':
                div.innerHTML = Recent.GetName( event.who ) + ' είναι καινούργιος στο Zino!';
                break;
            case 'Poll':
                div.innerHTML = Recent.GetName( event.who ) + ' δημιούργησε μία δημοσκόπηση';
                break;
            case 'Poll':
                div.innerHTML = Recent.GetName( event.who ) + ' έγραψε ημερολόγιο';
                break;
            case 'ImageTag':
                div.innerHTML = Recent.GetName( event.who ) + ' αναγνώρισε κάποιον σε μία φωτογραφία';
                break;
        }
        div.className = 'event';
        par.appendChild( div );
    },
    Process: function () {
        Recent.Now += Recent.Resolution;
        
        // document.title = 'Process ' + Recent.Events.length;
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
