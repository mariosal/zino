<?php
	function UnitUserJoin( tString $username , tString $password , tString $email ) {
		global $libs;
		
		$libs->Load( 'user/user' );
		$username = $username->Get();
		$password = $password->Get();
		$email = $email->Get();
		$finder = New UserFinder(); 
		if ( $finder->FindByName( $username ) ) {
			?>Join.usernameexists = true;
			Join.username.focus();
			Join.username.select();
			alert( "exists" );<?php
		}
		/*
		if ( Valid_User( $username ) ) {
			?>alert( 'OK' );<?php
		}
		*/
	}
?>
