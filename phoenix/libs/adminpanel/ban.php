<?php
    /*
        Developer:Pagio
    */
    
    class Ban {
    
        public function BanUser( $user_name ) {
            global $libs;
            global $db;
            
            $libs->Load( 'user/user' );        
            $libs->Load( 'adminpanel/bannedips' );
            $libs->Load( 'loginattempt' );
            
            $userFinder = new UserFinder();
            $banneduser = $userFinder->FindByName( $user_name );
            
            if ( !$banneduser ) {//if not existing user
                return false;
            }
            
            //trace relevant ips from login attempts
            $query = $db->Prepare( 
                'SELECT * FROM :loginattempts
                WHERE login_username=:username 
                GROUP BY  `login_ip`'
            );
            $query->BindTable( 'loginattempts' );
            $query->Bind( 'username' , $user_name );            
            $res = $query->Execute();
            
            $logs = array();
            while( $row = $res->FetchArray() ) {
                $log = new LoginAttempt( $row );
                $logs[] = $log->ip;
            }
            return $logs;
            
            //ban this ips and ban user with this username
            /*bannedip=new BannedIp();
             bannedip->BanIps( $logs );
             banneduser->DelId=1;
             
            */
           
            
            
            return true;
        }
    }
?>
