$( function() { 
    $( 'div#schview div.photos div.plist ul li a.uploadphoto' ).click( function() {
        var modal = $( '#uploadmodal' )[ 0 ].cloneNode( true );
        $( modal ).show();
        $( modal ).find( 'a.close' ).click( function() {
            Modals.Destroy();
            return false;
        } );
        Modals.Create( modal , 400 , 250 );
        return false;
    } );
} );
