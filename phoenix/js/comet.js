var Comet = {
    SubcriptionCallbacks: {},
    Connected: false,
    ConnectionTimer: 0,
    Connect: function () {
        if ( Comet.Connected ) {
            return;
        }
        if ( Comet.ConnectionTimer !== 0 ) {
            clearTimeout( Comet.ConnectionTimer );
        }
        Comet.ConnectionTimer = setTimeout( function () {
            Comet.Connected = true;
            Meteor.connect();
        }, 100 );
    },
    Subscribe: function ( channel, callback ) {
        Comet.Connect();
        Meteor.joinChannel( channel, 0 );
    },
    Process: function ( json ) {
        alert( 'Got data: ' + json );
        var obj = eval( '[' + json + ']' )[ 0 ];
        var channel = obj[ 0 ];
        var data = obj[ 1 ];
        
        if ( typeof Comet.SubcriptionCallbacks[ channel ] == 'function' ) {
            Comet.SubcriptionCallbacks[ channel ]( data );
        }
    },
    Init: function ( userid ) {
        Meteor.hostid = userid;
        Meteor.host = "universe." + location.hostname;
        Meteor.registerEventCallback( "process", Comet.Process );
        Meteor.registerEventCallback( 'pollmode', Comet.ChangeMode );
        Meteor.mode = 'stream';
    },
    ChangeMode: function ( mode ) {
        switch ( mode ) {
            case 'poll': // don't allow polling
                Meteor.disconnect();
                break;
        }
    }
};
