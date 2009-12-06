<?php
    class ControllerAlbum extends Controller {
        public function View( tInteger $id , tInteger $pageno ) {
            global $user;
            global $rabbit_settings; 
            global $water;
            global $libs;
            
            $libs->Load( 'album' );
            
            $album = New Album( $id->Get( array( 'min' => 1 ) ) );
            
            if ( !$album->Exists() ) {
                return Element( '404', 'To album δεν υπάρχει' );
            }

            if ( $album->Ownertype == TYPE_USERPROFILE ) {
                if ( $album->Owner->Deleted ) {
                    $this->Redirect( 'http://static.zino.gr/phoenix/deleted' );
                    return;
                }
                if ( Ban::isBannedUser( $album->Owner->Id ) ) {
                    $this->Redirect( 'http://static.zino.gr/phoenix/banned' );
                    return;
                }
            }

            $finder = New ImageFinder();
            $images = $finder->FindByAlbum( $album , ( $pageno - 1 ) * 20 , 20 );
            $page->SetTitle( $album->Name );

            Element( 'developer/album/view', $album, $images );
        }
    }
?>
