<?php

    function UnitPollNew( tString $question, tString $options, tCoalaPointer $callback ) {
        global $user;
        global $libs;

        $libs->Load( 'poll' );

        if ( $user->IsAnonymous() ) {
            return;
        }

        $poll               = new Poll();
        $poll->Question     = $question->Get();
        $poll->TextOptions  = split( "\|", $options->Get() );
        $poll->UserId       = $user->Id();
        $poll->Save();

        ob_start();
        Element( 'poll/view', $poll, $user );
        $html = ob_get_clean();

        echo $callback;
        ?>( <?php
        echo $html;
        ?> );<?php
    }

?>
