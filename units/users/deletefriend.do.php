<?php
	function UnitUsersDeletefriend( tInteger $friendid ) {
		global $user;
		global $xc_settings;
		
        $friendid = $friendid->Get();
        
//        $type = $user->GetRelId( $friendid );
		$user->DeleteFriend( $friendid );
		
/*		?>g( 'frel_<?php
		echo $type;
		?>' ).className = "frelation";
		g( 'frel_-1' ).className = "relselected"; <?php */ ?>
		g('friendadd').childNodes[1].firstChild.src = "<?php
		echo $xc_settings[ 'staticimagesurl' ];
        ?>icons/user_add.png";
        g('friendadd').childNodes[1].firstChild.onclick = function() {
        			Friends.AddFriend( <?php
        			echo $friendid;
        			?>, -1 );
        		};
		Friends.FriendDeleted( <?php
		echo $user->Id();
		?> );<?php
	}
?>
