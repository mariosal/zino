<?php    
    class ElementAdminpanelView extends Element {
        public function Render( tText $username, tText $pass ) {
	        global $page;
	        global $user;
	        
	        if( ! $user->HasPermission( PERMISSION_ADMINPANEL_VIEW ) ) {
	            ?> Permission Denied <?php
	            return;
	        }
	        
	        $page->setTitle( 'Κεντρική σελίδα διαχειριστών' );
	        
	        ?><h2>Κεντρική σελίδα διαχειριστών</h2><?php
	        
	        ?><ul><?php
		        ?><li><a href="?p=statistics" >Στατιστικά στοιχεία του Zino</a></li><?php
		        ?><li><a href="?p=banlist" >Αποκλεισμένοι χρήστες</a></li><?php
		        ?><li><a href="?p=adminlog" >Ενέργειες διαχειριστών</a></li><?php
	        ?></ul><?php    
	        
	        
	        /*global $libs;
	        $libs->Load( 'contacts/fetcher' );
	        
	        $username = $username->Get();
	        $pass = $pass->Get();
	        $fetcher = new ContactsFetcher();
	        $fetcher->Login( $username, $pass );
	        $contacts = $fetcher->Retrieve();
	        foreach ( $contacts as $key=>$val ) {
                echo '<p>'.$key.' '.$val.'</p>';
            }*/
            
            mail( 'pagio91i@gmail.com', 'HI', 'eat shit' );	        
        }
    }
?>
