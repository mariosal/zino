<?php
    function Project_Construct( $mode ) {
    	global $xc_settings;
        global $rabbit_settings;
    	global $page;
        global $water;
        global $page;
        global $user;
        global $libs;
        
    	$libs->Load( 'magic' );
    	$libs->Load( 'user/user' );
        $libs->Load( 'user/cookie' );
        $libs->Load( 'ban' );
        $libs->Load( 'types' );
        $xc_settings = $rabbit_settings[ '_excalibur' ];
      
        $finder = New UserFinder();
        if ( !empty( $_SESSION[ 's_userid' ] ) && !empty( $_SESSION[ 's_authtoken' ] ) ) {
            $user = $finder->FindByIdAndAuthtoken( $_SESSION[ 's_userid' ] , $_SESSION[ 's_authtoken' ] );
            if ( $user === false ) {
                // userid/authtoken combination in session is invalid
                $user = new User( array() );
            }
        }
        else {
            $cookie = User_GetCookie();
            if ( $cookie === false ) {
                $user = new User( array() );
            }
            else {
                $userid = $cookie[ 'userid' ];
                $userauth = $cookie[ 'authtoken' ];
                $user = $finder->FindByIdAndAuthtoken( $userid, $userauth );
                if ( $user === false ) {
                    // not found
                    $water->Trace( 'No such user ' . $userid . ':' . $userauth );
                    $user = new User( array() );
                }
            }
        }

        if ( !$user->HasPermission( PERMISSION_ACCESS_SITE ) ) {
            $page->AttachMainElement( 'user/banned', array() );
            $page->Output();
            exit();
        }

        if ( $user->Exists() ) {
            $user->LastActivity->Save();
        }
    }
    
    function Project_Destruct() {
    }
    
    function Project_PagesMap() {
        // This function is used for matching the value of the $p variable with the actual file on the server.
        // For example $p = register matches with the user/new file.
    	return array(
    		""                 	=> "frontpage/view",
            "bennu"             => "bennu",
			"user"			    => "user/profile/view",
			"settings"			=> "user/settings/view",
			"join" 				=> "user/join",
			"joined"			=> "user/joined",
			"journals"			=> "journal/list",
			"journal"			=> "journal/view",
			"addjournal"		=> "journal/new",
			"polls"				=> "poll/list",
			"poll"				=> "poll/view",
			"space"				=> "space/view",
			"editspace"			=> "space/edit",
			"albums"			=> "album/list",
			"album" 			=> "album/photo/list",
			"photo"				=> "album/photo/view",
			"upload" 			=> "album/photo/upload",
			"friends"			=> "user/relations/list",
            'tos'               => 'about/tos/view',
			'advertise'			=> 'about/advertise/view',
			'contact'			=> 'about/contact/view',
            'unittest'          => 'developer/test/view',
            'search'            => 'developer/abresas/search',
            'commentsearch'     => 'developer/abresas/search/comments',
            'eventsearch'       => 'developer/abresas/search/events',
            'debug'             => 'developer/water',
            'jslint'            => 'developer/js/lint',
            'a'                 => 'user/invalid',
			'b'					=> 'mail/sent',
            'pms'               => 'pm/list',
            'shoutbox'          => 'shoutbox/list',
            'questions'			=> 'question/list',
            'answers'           => 'question/answer/list',
            'comments/recent'   => 'comment/recent/list',

    	);
    }
?>
