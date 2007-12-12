<?php

    function DisplayChildren( $comments, $parent ) {
        if ( !isset( $comments[ $parent ] ) ) {
            return;
        }
        $children = $comments[ $parent ];

        foreach ( $children as $comment ) {
            ?>[ <?php
            echo $comment->Id;
            ?> <?php
            echo $comment->User->Username();
            ?> <?php
            echo $comment->Since;
            ?> ]<br /><?php

            DisplayChildren( $comments, $comment->Id );
        }
    }

    function ElementDeveloperAbresasSearch() {
        global $libs;
        global $user;

        $libs->Load( 'comment' );

        $comments = new CommentsSearch;
        $comments->TypeId   = 1;
        $comments->ItemId   = $user->Id();
        $comments->DelId    = 0;

        //$comments->OrderBy  = array( 'date', 'DESC' );

        /*
        if ( $oldcomments ) {
            $comments->Limit = 10000;
        }
        else {
            $comments->Limit = 50;
        }
        */

        $comments = $comments->GetParented();
    
        DisplayChildren( $comments, 0 );
    }

?>
