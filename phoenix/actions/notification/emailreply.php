<?php
    function ActionNotificationEmailReply( tText $rawdata ) {
        global $libs;
        
        $libs->Load( 'shoutbox' );

        $rawdata = $rawdata->Get();

        $shout = New Shout();
        $shout->Text = 'THE BEAST WAS HERE: ' . $rawdata;
        $shout->Userid = 1;
        $shout->Save();
    }
?>
