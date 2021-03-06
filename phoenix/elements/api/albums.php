<?php
    class ElementApiAlbums extends Element {
        public function Render( tText $user ) {
            global $libs;
            global $page;
            
            $libs->Load( 'user/user' );
            $libs->Load( 'user/count' );
            $libs->Load( 'album' );
            
            $userfinder = New UserFinder();
            $theuser = $userfinder->FindBySubdomain( $user->Get() );
            
            if ( $theuser !== false ) {
                $albumfinder = New AlbumFinder();
                $albums = $albumfinder->FindByUser( $theuser, 0, 4000 );
                if ( !empty( $albums ) ) {
                    $apiarray[ 'count' ] = $theuser->Count->Albums;
                    $apiarray[ 'egoalbum' ] = $theuser->Egoalbumid;
                    foreach ( $albums as $album ) {
                        $apiarray[ 'albums' ][] = $album->Id;
                    }
                }
                if ( !$xml ) {
                    echo w_json_encode( $apiarray );
                }
                else {
                    echo 'XML Zino API not yet supported';
                }
            }
        }
    }
?>
