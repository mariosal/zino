<?php
    function ActionAdminpanelBan( tText $username, tText $reason, tText $time_banned, tText $delete_journals ) {
        global $libs;
        
        $username = $username->Get();
        $reason = $reason->Get();
        $time_banned = $time_banned->Get();
        $delete_journals = $delete_journals->Get();
        
        if ( $reason == "" ) {
            $reason = "Δεν αναφέρθηκε";
        }
        
        $time_banned = $time_banned*24*60*60;//<--make days to secs
        
        $libs->Load( 'adminpanel/ban' );
        
        $ban = new Ban();
        $res = $ban->BanUser( $username, $reason, $time_banned );
        
        return Redirect( '?p=banlist&'.$delete_journals );
        
        if ( $delete_journals == "yes" ) {
            return Redirect( '?p=banlist&deletejournals' );;//deletejournals
        }
           
        return Redirect( '?p=banlist' );
    }
?>
