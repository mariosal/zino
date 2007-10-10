<?php
	function ElementUniversitiesUnipertownlist( $townid ) {
		global $libs;
		global $water;
		
		$libs->Load( 'universities' );
		
		$unis = Uni_Retrieve( $townid , false );
		if ( count( $unis ) == 0 ) {
			?>Δεν υπάρχουν εκπαιδευτικά ιδρύματα<?php
		}
		else {
			?><select id="modalunisel" onchange="Uni.SaveUni();return false;"><?php
			foreach( $unis as $uni ) {
				?><option value="<?php
				echo $uni->Id;
				?>"><?php
				echo $uni->Name;
				?></option><?php
			}
			?></select><?php
		}
	}
?>