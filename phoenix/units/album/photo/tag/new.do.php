<?php
    function UnitAlbumPhotoTagNew( tInteger $photoid, tText $username, tInteger $left, tInteger $top, tInteger $width, tInteger $height, tCoalaPointer $callback ) {
        global $user;
        global $libs;

        $libs->Load( 'relation/relation' );
        $libs->Load( 'image/tag' );

        if ( !$user->Exists() ) {
            // require login
            return;
        }

        $photoid = $photoid->Get();
        $username = $username->Get();
        $left = $left->Get();
        $top = $top->Get();
		$width = $width->Get();
		$height = $height->Get();

        $photo = New Image( $photoid );
        $userfinder = New UserFinder();
        $theuser = $userfinder->FindByName( $username );

        if ( !$theuser->Exists() ) {
            ?>alert( 'Ο χρήστης αυτός είναι αποκύημα της φαντασίας σας' );
            window.location.reload();<?php
            return;
        }
        if ( !$photo->Exists() ) {
            ?>alert( 'Η φωτογραφία που προσπαθείται να tagαρεται δεν υπάρχει' );
            window.location.reload();<?php
            return;
        }
		if ( $width < 45 || $height < 45 ) {
			?>alert( 'Οι συγκεκριμένες διαστάσεις δεν είναι έγκυρες' );
			window.location.reload();<?php
			return;
		}
        // check for permissions
        $photoowner = $photo->User;

        $relationfinder = New FriendRelationFinder();
        // check if user is owner of photo or friend of owner; you can't tag some unknown person's photos
        if ( $photoowner->Id != $user->Id 
             && $relationfinder->IsFriend( $photoowner, $user ) != FRIENDS_BOTH ) {
             ?>alert( 'Δεν έχεις καμία σχέση με τον κάτοχο της φωτογραφίας' );
             window.location.reload();<?php
             return;
        }

        // now check that the tagged person is the friend of the user; you can't tag who doesn't know you
        if ( $relationfinder->IsFriend( $theuser, $user ) != FRIENDS_BOTH && $theuser->Id != $user->Id ) {
            ?>alert( 'Ο συγκεκριμένος χρήστης δεν έχει καμία σχέση μαζί σας' );
            window.location.reload();<?php
            return;
        }
        // all OK, proceed
        $tag = New ImageTag();
        $tag->Imageid = $photoid;
        $tag->Personid = $theuser->Id;
        $tag->Ownerid = $user->Id;
        $tag->Left = $left;
        $tag->Top = $top;
        $tag->Width = $width;
        $tag->Height = $height;
        $tag->Save();
        
        echo $callback;
        ?>( <?php
        echo $tag->Id;
        ?>, <?php
        echo w_json_encode( $username );
        ?>, <?php
        echo w_json_encode( $theuser->Subdomain );
        ?> );<?php
    }
?>
