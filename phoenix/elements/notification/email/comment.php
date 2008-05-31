<?php
    /// Content-type: text/plain ///
	function ElementNotificationEmailComment( User $from, Comment $comment ) {
        w_assert( $from instanceof User );
        w_assert( $from->Exists() );

        if ( $from->Gender == 'f' ) {
            ?>Η<?php
        }
        else {
            ?>Ο<?php
        }
        ?> <?php
        echo $from->Name;
        if ( $comment->Parentid ) {
            ?> απάντησε στο σχόλιό σου <?php
        }
        else {
            ?> σχολίασε <?php

            switch ( $comment->Typeid ) {
                case TYPE_JOURNAL:
                    ?>στο ημερολόγιό σου "<?php
                    echo $comment->Item->Title;
                    ?>" <?php
                    break;
                case TYPE_IMAGE:
                    ?>στην εικόνα σου<?php
                    if ( !empty( $comment->Item->Name ) ) {
                        ?> "<?php
                        echo $comment->Item->Name;
                        ?>"<?php
                    }
                    break;
                case TYPE_USER:
                    ?>στο προφίλ σου<?php
                    break;
                case TYPE_POLL:
                    ?>στην δημοσκόπισή σου "<?php
                    echo $poll->Question;
                    ?>"<?php
                    break;
            }
        }
        ?> και έγραψε:
        
        "<?php
        echo $comment->Text;
        ?>"

        Για να απαντήσεις στο σχόλιό <?php
        if ( $from->Gender == 'f' ) {
            ?>της<?php
        }
        else {
            ?>του<?php
        }
        ?> <?php
        echo $from->Name;
        ?> κάνε κλικ στον παρακάτω σύνδεσμο:
        <?php
        Element( 'url', $comment );

        Element( 'notification/email/footer' );
	}
?>
