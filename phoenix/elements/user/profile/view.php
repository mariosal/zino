<?php
	function ElementUserProfileView( tString $name , tString $subdomain ) {
		global $page;
		
		$name = $name->Get();
		$subdomain = $subdomain->Get();
		$finder = New UserFinder();
		if ( $name != '' ) {
			$theuser = $finder->FindByName( $name );
		}
		else {
			$theuser = $finder->FindBySubdomain( $subdomain );
		}
		if ( $theuser === false ) {
			return Element( '404' );
		}
		die( get_class( $page ) );
		$page->AttachStyleSheet( 'css/user/profile/view.css' );
		$page->SetTitle( $theuser->Name );
		?><div id="profile"><?php
			Element( 'user/profile/sidebar/view' , $theuser );
			Element( 'user/profile/main/view' , $theuser );
			?><div class="eof"></div>
		</div><?php
	}
?>