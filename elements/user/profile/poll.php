<?php

    function ElementUserProfilePoll( $theuser ) {
        global $user;
        global $libs;
        global $page;

        $libs->Load( 'poll' );
        $page->AttachStylesheet( 'css/poll.css' );

        if ( $user->IsAnonymous() ) {
            return;
        }

        $polls = Poll_GetByUser( $theuser );

        while ( $poll = array_shift( $polls ) ) {
            if ( $poll->DelId == 0 ) {
                break;
            }
        }

        if ( !count( $polls ) || $poll->DelId == 0 ) {
            return Element( 'user/profile/poll/new', $theuser );
        }

        if ( !$poll->UserHasVoted( $user ) ) {
            Element( 'user/profile/poll/view', $polls[ 0 ], $theuser );
        }
        else {
            Element( 'user/profile/poll/results', $polls[ 0 ] );
        }
    }

?>
