<?php
    define( 'COMET_PUBLISHING_SERVER', '147.102.133.127' ); // 'universe.zino.gr' );
    define( 'COMET_PUBLISHING_PORT', 4671 );
    define( 'COMET_PUBLISHING_CONNECTION_TIMEOUT', 5 );
    define( 'COMET_PUBLISHING_TIMEOUT', 2 );
    
    class CometException extends Exception {
    }
    
    function Comet_Publish( /* $channel, $param1, $param2, ..., $paramN */ ) {
        $args = func_get_args();
        $channel = $args[ 0 ];
        $message = $args; // keep channel in message
        
        w_assert( is_string( $channel ) );
        w_assert( preg_match( '#[A-Za-z0-9_]+#', $channel ) );
        
        $fh = fsockopen( COMET_PUBLISHING_SERVER, COMET_PUBLISHING_PORT, $errno, $errstr, COMET_PUBLISHING_CONNECTION_TIMEOUT );
        if ( !$fh ) {
            throw New CometException( 'Could not initiate socket connection to publishing server: ' . $errstr . ' (' . $errno . ')' );
        }
        stream_set_timeout( $fh, COMET_PUBLISHING_TIMEOUT );
        $message = w_json_encode( $message );
        fwrite( $fh, "ADDMESSAGE $channel $message\n" );
        /*
        $response = '';
        while ( !feof( $fh ) ) {
            $response .= fread( $fh, 8192 );
        }
        */
        fclose( $fh );
    }
?>
