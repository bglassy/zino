<?php
    echo "start\n";
    global $water;

    $offset = ( integer )$_GET[ 'offset' ];
    $limit = 100;

    set_include_path( '../:./' );

    require '../libs/rabbit/rabbit.php';

    Rabbit_Construct();

    echo "rabbit constructed\n";
    global $libs;
    global $db;

    $libs->Load( 'url' );

    $query = $db->Prepare( 'SELECT * FROM :albums' );
    $query->BindTable( 'albums' );
    $res = $query->Execute();

    $albums = array();
    while ( $row = $res->FetchArray() ) {
        $ownerId = $row[ 'album_ownerid' ];
        if ( !isset( $albums[ $ownerId ] ) ) {
            $albums[ $ownerId ] = array();
        }
        $albums[ $ownerId ][] = array(
            'id' => $row[ 'album_id' ],
            'name' => $row[ 'album_name' ]
        );
    }
    echo "moved on\n";
    $result = array();
    foreach ( $albums as $ownerId => $hisalbums ) {
        $urls = array();
        foreach ( $hisalbums as $albumInfo ) {
            $candidate = URL_Format( $albumInfo[ 'name' ] );
            while ( isset( $urls[ $candidate ] ) ) {
                $candidate .= '_';
            }
            $urls[ $candidate ] = true;
            $result[ $albumInfo[ 'id' ] ] = $candidate;
        }
    }
    echo "moved more on\n";
    $i = 0;
    foreach ( $result as $id => $url ) {
        if ( $i >= $offset && $i <= $offset + $limit ) {
            $query = $db->Prepare(
                'UPDATE
                    :albums 
                SET
                    `album_url` = :album_url
                WHERE
                    `album_id` = :album_id
                LIMIT 1;'
            );
            $query->BindTable( 'albums' );
            $query->Bind( 'album_url', $url );
            $query->Bind( 'album_id', $id );
            $query->Execute();
        }
        ++$i;
    }
    echo "before writing\n";
    if ( $offset + $limit <= count( $result ) ) {
        $offset += $limit;
        ?><html><head><name>Processing...</name>
        </head><body>
        Processed <?php
        echo $offset;
        ?> out of <?php
        echo count( $result );
        ?>.<?php
        $water->Post();
        ?>
        <script type="text/javascript">
        setTimeout( function() {
            window.location.href = "prettyalbumsurls.php?offset=<?php
            echo $offset;
            ?>";
        }, 1000 );
        </script>
        </body></html><?php
    }

    Rabbit_Destruct();

?>
