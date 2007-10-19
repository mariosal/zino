<?php
    function ElementMain() {
        global $user;
        global $water;
        global $page;
        global $libs;
        
        $page->AttachStylesheet( 'css/main.css' );
        $page->AttachScript( 'js/pngfix.js' , 'javascript', false, '7' );
        $page->AttachScript( 'js/main.js' );
        $page->AddMeta( 'author', 'Kamibu Development Team' );
        $page->AddMeta( 'keywords', 'greek friends chat community greece meet people' );
        $page->AddMeta( 'description', '�� Chit-Chat ����� ��� �������� ��������� ����� - ����� ����;' );
        
        ?><div class="axel"><?php
        ob_start();
        $res = MasterElement();
        $master = ob_get_clean();
        
        if ( $res === false ) { // If the page requested is not in the pages available by pagemap
            Element( 'banner' );
            Element( '404' );
            Element( 'copyright' );
        }
        else {
            if ( !is_array( $res ) || !isset( $res[ 'tiny' ] ) ) {
                Element( 'banner' );
            }
            echo $master;
            if ( !is_array( $res ) || !isset( $res[ 'tiny' ] ) ) {
                Element( 'copyright' );
            }
        }
        ?></div><?php
        Element( 'tracking/analytics' ); // Google-Analytics, for stats
        if ( $page->Title() != '' ) { // If the title's page is not blank
            $page->SetTitle( $page->Title() . ' / Chit-Chat' );
        }
        else {
            $water->Notice( 'Title not defined for page' ); // Produce a notice at the php debugger
            $page->SetTitle( 'Chit-Chat' );
        }
        
        // pass
        return $res;
    }
?>
