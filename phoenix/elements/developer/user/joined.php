<?php
    class ElementDeveloperUserJoined extends Element {
        public function Render() {
            global $user;
            global $rabbit_settings;
            global $page;
            global $libs;
            
            $libs->Load( 'rabbit/helpers/http' );
            
            $page->SetTitle( 'Καλωσήρθες' );
            
            if ( !$user->Exists() ) {
                return Redirect( $rabbit_settings[ 'webaddress' ] );
            }
            ?><div id="joined">
                <div class="ybubble">
                    <div class="body">
                        <div class="welcome">
                            <div>Συγχαρητήρια! Mόλις δημιουργήσες λογαριασμό στο <?php
                            echo $rabbit_settings[ 'applicationname' ];
                            ?>.<br />
                            To προφίλ σου είναι <b><?php
                            Element( 'developer/user/url' , $user->Id , $user->Subdomain );
                            ?></b></div>
                            <i class="s2_0004 bl"></i>
                            <i class="s2_0003 br"></i>
                        </div>
                    </div>
                </div>
                <div class="profinfo">
                    <p>
                    Συμπλήρωσε μερικές λεπτομέρειες για τον εαυτό σου
                    </p>
                    <form>
                        <div>
                            <span>Ημερομηνία γέννησης:</span><?php
                            Element( 'developer/user/settings/personal/dob' );
                        ?><span class="invaliddob"><span class="s1_0034">&nbsp;</span>Η ημερομηνία δεν είναι έγκυρη</span>
						</div>
                        <div>
                            <span>Φύλο:</span><?php
                            Element( 'developer/user/settings/personal/gender' , $user->Gender );
                        ?></div>
                        <div>
                            <span>Περιοχή:</span><?php
                            Element( 'developer/user/settings/personal/place' , $user->Profile->Placeid );
                        ?></div>
                    </form>
                </div>
                <div style="text-align:center">
                    <a href="" class="button button_big">Συνέχεια &raquo;</a>
                </div>
            </div><?php
            $page->AttachInlineScript( 'Joined.JoinedOnLoad();' );
        }
    }
?>
