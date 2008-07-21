<?php
	function UnitUserSettingsTagsSuggest( tString $text, tString $type, tCoalaPointer $callback ) {
		global $libs;
		
		$libs->Load( 'tag' );
		
		$text = $text->Get();
		$type = $type->Get();
		
		switch( $type ) {
			case 'hobbies':
				$act_type = TAG_HOBBIE;
				break;
			case 'movies':
				$act_type = TAG_MOVIE;
				break;
			case 'books':
				$act_type = TAG_BOOK;
				break;
			case 'songs':
				$act_type = TAG_SONG;
				break;
			case 'artists':
				$act_type = TAG_ARTIST;
				break;
			case 'games':
				$act_type = TAG_GAME;
				break;
			case 'shows':
				$act_type = TAG_SHOW;
				break;
			default:
				$type = -1;
		}
		
		$finder = New TagFinder();
		$res = $finder->FindSuggestions( $text, $act_type );
		
		/*
		$arr = "{ ";
		$len = count( $res );
		for( $i = 0; $i < $len; ++$i ) {
			$arr .= $res[ $i ];
			if ( $i != $len-1 ) {
				$arr .= ", ";
			}
		}
		$arr .= "}"; */
		$k=0;
		foreach( $res as $i ) {
		?>alert( "<?php
		echo $k;
		?>" + <?php
		echo w_json_encode( $i );
		?> );<?php
		++$k;
		}
		/*
		echo $callback;
		?>( <?php
		echo w_json_encode( $type );
		?>, <?php
		echo w_json_encode( $res );
		?> );<?php */
	}
?>
