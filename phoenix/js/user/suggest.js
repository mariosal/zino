var Suggest = {
	timeoutid : { // During the execution of the code, this array holds the setTimeOut id's for each suggestion type
		'hobbies' : false,
		'movies' : false,
		'books' : false,
		'songs' : false,
		'artists' : false,
		'games' : false,
		'shows' : false
	},
	selectMove : function( event, type ) {
		if ( $( 'div.' + type + ' form' ).css( "display" ) == "none" ) {
			return;
		}
		var sel = $( 'div.' + type + ' select' );
		var selindex = sel.attr( "selectedIndex" );
		if ( selindex === undefined ) {
			selindex = 0;
		}
		// If Up or Down is pressed TODO: prevent input's onkeyup from firing
		if ( ( selindex === 0 && event.keyCode == 38 ) || ( selindex == sel.get(0).options.length-1 && event.keyCode == 40 ) ) {
			$( 'div.' + type + ' input' ).focus();
		}
		else if ( event.keyCode == 13 ) {
			var text = sel.get( 0 ).options[ selindex ].value;
			$( 'div.' + type + ' input' ).val( text ).focus();
			sel.find( 'option' ).remove();
			$( 'div.' + type + ' form' ).hide();
		}
	},
	inputMove : function( event, type ) {
		if ( $( 'div.' + type + ' form' ).css( "display" ) == "none" ) {
			return;
		}
		var sel = $( 'div.' + type + ' select' );
		if ( event.keyCode == 40 ) {
			sel.attr( 'selectedIndex', 0 );
			sel.focus();
		}
		else if ( event.keyCode == 38 ) {
			sel.attr( 'selectedIndex', sel.get(0).options.length-1 );
			sel.focus();
		}
		else {
			$( 'div.' + type + ' form' ).hide();
			sel.find( 'option' ).remove();
		}
	},
	suggestCallback : function( type, suggestions ) {
		if ( suggestions.length == undefined ) {
			return;
		}
		$( 'div.' + type + ' form' ).show();
		var sel = $( 'div.' + type + ' select' ).get( 0 );
		sel.size = ( suggestions.length >= 5 )?5:suggestions.length;
		for( var i in suggestions ) {
			var opt = document.createElement( 'option' );
			opt.value = suggestions[i];
			opt.onclick = function() {
				var typeid;
				switch( type ) {
					case 'hobbies':
						typeid = 1;
						break;
					case 'movies':
						typeid = 2;
						break;
					case 'books':
						typeid = 3;
						break;
					case 'songs':
						typeid = 4;
						break;
					case 'artists':
						typeid = 5;
						break;
					case 'games':
						typeid = 6;
						break;
					case 'shows':
						typeid = 7;
						break;
					default:
						typeid = -1;
				}
				if ( typeid == -1 ) {
					return;
				}
				//$( 'div.' + type + ' input' ).focus().get( 0 ).value = this.value;
				$( 'div.' + type + ' input' ).focus().get( 0 ).value = '';
				Settings.AddInterest( this.value, typeid );
				$( 'div.' + type + ' form' ).hide().find( 'option' ).remove();
			}
			opt.onmouseover = function() {
				this.style.backgroundColor = '#E4EAF9';
			}
			opt.onmouseout = function() {
				this.style.backgroundColor = 'white';
			}
			opt.appendChild( document.createTextNode( suggestions[i] ) );
			sel.appendChild( opt );
		}
	},
	fire : function( event, type ) {
		if ( event.keyCode == 38 || event.keyCode == 40 ) {
			return;
		}
		var text = $( 'div.' + type + ' input' ).val();
		if ( Suggest.timeoutid[ type ] !== false ) {
			window.clearTimeout( Suggest.timeoutid[ type ] );
		}
		if ( event.keyCode == 13 || $.trim( text ) == '' ) { // Leave keyCode==13 here. Otherwise suggestions will appear after the interest is added
			return;
		}
		Suggest.timeoutid[ type ] = window.setTimeout( "Coala.Cold( 'user/settings/tags/suggest', { 'text' : '" + text + "', 'type' : '" + type + "', 'callback' : Suggest.suggestCallback } );", 1500 );
	}
}
