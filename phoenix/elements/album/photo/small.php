<?php
	
	function ElementAlbumPhotoSmall( $image , $showdesc = false, $showfav = false, $showcomnum = false ) {
		$size = $image->ProportionalSize( 210 , 210 );
		if ( $image->Name != '' ) {
			$title = htmlspecialchars( $image->Name );
		}	
		else {
			$title = htmlspecialchars( $image->Album->Name );
		}
		?><div class="photo">
			<a href="?p=photo&amp;id=<?php
			echo $image->Id;
			?>"><?php
				Element( 'image' , $image , $size[ 0 ] , $size[ 1 ] , '' , $title , $title ); 
				if ( $showdesc && $image->Name != '') {
					?><br /><?php
					echo htmlspecialchars( $image->Name );
				}
			?></a><?php
			if ( $showfav || $showcommnum ) {
				?><div><?php
					if ( $showfav ) {
						?><span class="addfav"><a href="" onclick="return false;" title="Προσθήκη στα αγαπημένα"></a></span><?php
					}
					if ( $showcomnum ) {
						?><span class="commentsnum">87</span><?php
					}
				?></div><?php
			}
		?></div><?php
	}
?>
