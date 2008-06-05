<?php
	
	function ElementUserRelationsList( tInteger $id , tInteger $offset ) {
		global $libs;
		$libs->Load( 'relation' );
		
		$offset = $offset->Get();
		
		$theuser = New User( $id->Get() );
		$finder = New RelationFinder();
		$friends = $finder->FindByUser( $theuser , 0 , 20 ); 
		?><div id="relations">
			<h3>Σχέσεις</h3><?php
			Element( 'user/list' , $friends );
		?></div><?php
	
	}
?>
