<?php
spl_autoload_register( function( $class ) {
    if( strpos( $class, "Unconv\\WPChatGPT\\" ) === 0 ) {
        $class = str_replace( "\\", "/", $class );
        $class = substr( $class, 17 );
        require( __DIR__ . "/src/" . $class . ".php" );
    }
} );
