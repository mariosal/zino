<?php
	function UnitUserJoin( tString $username , tString $password , tString $email ) {
		$username = $username->Get();
		$password = $password->Get();
		$email = $email->Get();
		if ( Valid_User( $username ) ) {
			?>alert( 'OK' );<?php
		}
	}
?>
