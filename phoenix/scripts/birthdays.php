<?php
    set_include_path( '../:./' );
    
    global $water;
    global $libs;
    global $page;
    
    require '../libs/rabbit/rabbit.php';
    
    Rabbit_Construct();
    
    $water->Enable(); // on for all
    
    header( 'Content-Type: text/html; charset=utf-8' );
    
    $libs->Load( 'user/user' );
    $libs->Load( 'event' );
    
    date_default_timezone_set( 'Europe/Athens' );
    
    $finder = new UserFinder();
    $arr =  $finder->FindByBirthday( (int)date( 'm' ), (int)date( 'd' ), 0, 1000 );
    
    foreach ( $arr as $row ) {
        $event = New Event();
        $event->Typeid = EVENT_USER_BIRTHDAY;
        $event->Userid = $row[ 0 ];
        $event->Itemid = $row[ 1 ];
        $event->Save();
    }
?>
Done!
