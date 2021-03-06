<?php

    function UnitPollUndoDelete( tInteger $pollid, tCoalaPointer $callback ) {
        global $user;
        global $libs;

        $libs->Load( 'poll' );

        $poll = New Poll( $pollid->Get() );
        if ( !$poll->Exists() || $user->IsAnonymous() || $poll->UserId != $user->Id() ) {
            return;
        }
        $poll->DelId = 0;
        $poll->Save();
        
        ob_start();

        Element( 'poll/box', $poll, $user );

        $html = ob_get_clean();

        echo $callback;
        ?>( <?php
        echo w_json_encode( $html );
        ?> );<?php
    }

?>
