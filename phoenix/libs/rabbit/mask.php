<?php
    /*
        Developer: Dionyziz 
    */
    
    class RabbitIncludeException extends Exception {
    }
    
    function Rabbit_Include( $filename ) {
        global $water;
        global $rabbit_settings;
        
        if ( !isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) { // debug
            echo "Rabbit including file: " . $filename . "\n";
        }
        // resolve into full path
        $filename = $rabbit_settings[ 'rootdir' ] . '/' . $filename;
        
        // apply masking and check for existance
        $maskres = Mask( $filename, !$rabbit_settings[ 'production' ] );
        
        // include and pass the return value up the callchain
        return Rabbit_IncludeReal( $maskres[ 'realpath' ] );
    }
    
    function Rabbit_IncludeReal( /* $filename */ ) {
        // force no variables -- $filename is avoided using func_get_args()
        // include and pass the return value up the callchain
        if ( !isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) { // debug
            echo "Rabbit including from filesystem: " . func_get_arg( 0 ) . "\n";
        }
        w_assert( func_num_args() == 1 );
        return require_once func_get_arg( 0 );
    }
    
    function Mask( $filename, $allowmasked = false , $extension = '.php' ) {
        if ( !isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) { // debug
            echo "Determining mask status of file: " . $filename . "\n";
        }
        $tail = basename( $filename );
        $till = strlen( $filename ) - strlen( $tail ) - 1;
        if ( $till <= 0 ) {
            $body = '';
        }
        else {
            $body = substr( $filename, 0, $till ) . '/'; 
        }
        if ( substr( $tail , 0 , 1 ) == '_' ) {
            if ( !isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) { // debug
                echo "Unmasking cannot be forced: " . $filename . "\n";
            }
            // unmasking cannot be forced
            throw New RabbitIncludeException( 'Unmasking cannot be forced' );
        }
        $fileexists = false;
        if ( $allowmasked ) {
            $maskedpath = $body . '_' . $tail . $extension;
            $fileexists = file_exists( $maskedpath );
            if ( $fileexists ) {
                if ( !isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) { // debug
                    echo "Found mask for file: " . $filename . "\n";
                }
                return array(
                    'masked' => true,
                    'realpath' => $maskedpath
                );
            }
        }
        if ( !$fileexists ) {
            $unmaskedpath = $body . $tail . $extension;
            $fileexists = file_exists( $unmaskedpath );
            if ( $fileexists ) {
                if ( !isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) { // debug
                    echo "Found production for file: " . $filename . "\n";
                }
                return array(
                    'masked' => false,
                    'realpath' => $unmaskedpath
                );
            }
        }
        if ( !isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) { // debug
            echo "Mask status failed, file not found: " . $filename . "\n";
        }
        throw New RabbitIncludeException( 'File not found: ' . $unmaskedpath );
    }    
?>
