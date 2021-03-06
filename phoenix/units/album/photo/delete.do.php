<?php
    function UnitAlbumPhotoDelete( tInteger $photoid ) {
        global $user;
        global $rabbit_settings;
        global $libs;
        
        $libs->Load( 'image/image' );
        
        $image = New Image( $photoid->Get() );
        if ( $image->Userid == $user->Id || $user->HasPermission( PERMISSION_IMAGE_DELETE_ALL ) ) {
            $albumid = $image->Albumid;
            $image->Delete();
            if ( $albumid > 0 ) {
                ?>window.location.href = '<?php
                echo $rabbit_settings[ 'webaddress' ];
                ?>?p=album&id=<?php
                echo $albumid;
                ?>';<?php
            }
        }
    }
?>
