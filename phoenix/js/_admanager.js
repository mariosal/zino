var AdManager = {
    Create: {
        OnLoad: function() {
            $( $( 'div.buyad a.start' )[ 0 ] ).click( function () {
                $( 'div.buyad form' ).submit();
            } );
            $( "#adtitle" ).keyup( function () {
                var a = $( "div.adspreview div.ad h4 a" )[ 0 ];
				var list = a.childNodes;
				for ( var i = 0; i < list.length; ++i ) {
					a.removeChild( list[ i ] );
				}
				var text = document.createTextNode( $( "#adtitle" )[ 0 ].value );
				a.appendChild( text );
            } );
        }
    }
};
