<?php
    function UnitUserProfileSelectsong( tInteger $songid ) {
        global $libs;
		global $user;

        $libs->Load( 'music/grooveshark' );
		
        $songid = $songid->Get();
		Grooveshark_SetSong( $songid );
		?>Profile.Player.Setsong( <?php
		ob_start();
		Element( 'user/profile/sidebar/flash', $user->Profile->Songwidgetid );
		echo w_json_encode( ob_get_clean() );
		?> );<?php
    }
?>