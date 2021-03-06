<?php

    class ElementDeveloperAbresasOnline extends Element {
        public function Render() {
            global $libs;
            global $xc_settings;

            $libs->Load( 'user/user' );
            $libs->Load( 'image/image' );

            $finder = New UserFinder();
            $onlineUsers = $finder->FindOnlineWithDetails();

            ?><h2>Online</h2><?php

            foreach ( $onlineUsers as $onlineUser ) {
                ?><div id="float: left;"><?php
                Element( 'user/avatar', $onlineUser[ 'image_id' ], $onlineUser[ 'user_id' ], 100, 100, $onlineUser[ 'user_name' ], IMAGE_CROPPED_100x100, '', '', true, 100, 100 );
                ?></div><div><a href="<?php
                Element( 'user/url', $onlineUser[ 'user_id' ], $onlineUser[ 'user_subdomain' ] );
                ?>"><?php
                echo $onlineUser[ 'user_name' ];
                ?></a> "<?php
                echo $onlineUser[ 'profile_slogan' ];
                ?>" <br /><img src="<?php
                echo $xc_settings[ 'staticimagesurl' ];
                ?>moods/<?php
                echo $onlineUser[ 'mood_url' ];
                ?>" /></div><br style="clear: both;" /><?php
            }
        }
    }

?>
