<?php
    /*
        MASKED
        By: Izual
        Reason: Spriting

        By: Dionyziz
        Reason: Report abuse

        By: Dionyziz
        Reason: Emblems
    */

    class ElementUserProfileSidebarDetails extends Element {
        protected $mPersistent = array( 'theuserid', 'lastupdated' );
        
        public function Render( $theuser, $theuserid, $lastupdated ) {
            $profile = $theuser->Profile;
            ?><div class="look">
				<span class="s1_0054">&nbsp;</span><?php
                Element( 'user/profile/sidebar/look', $profile->Height, $profile->Weight,  $theuser->Gender );
            ?></div>
            <div class="social"><?php
                Element( 'user/profile/sidebar/social/view' , $theuser );
            ?></div><?php
				if ( $theuserid == 872 )
					Element( 'user/profile/sidebar/player', $theuser );
			?><div class="aboutme"><?php
                Element( 'user/profile/sidebar/aboutme' , $profile->Aboutme );
            ?></div>
            <div class="interests"><?php
                Element( 'user/profile/sidebar/interests' , $theuser );
            ?></div>
            <div class="contacts"><?php
                /*Removed by: Chorvus
                  Reason: to counter web-crawlers searching for IMs
                  Element( 'user/profile/sidebar/contacts' , $profile->Skype , $profile->Msn , $profile->Gtalk , $profile->Yim ); */
            ?></div><?php
            $purchases = StorepurchaseFinder( $theuserid );
            var_dump( $purchases );
            die();
            if ( !empty( $purchases ) ) {
                ?><div class="supporter">
                    <img src="http://static.zino.gr/phoenix/emblems/bullet_orange.png" alt="Ðïñôïêáëß ôåëßôóá" />
                    ÕðïóôçñéêôÞò Zino Êáëïêáßñé 2009
                </div><?php
            }
            ?>
            <div id="reportabuse"><?php
                Element( 'user/profile/sidebar/abuse', $theuser->Id );
            ?></div><?php
        }
    }
?>
