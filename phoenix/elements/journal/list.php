<?php
	
	function ElementJournalList( tString $username ) {
		global $page;
		global $rabbit_settings;
		
		$username = $username->Get();
		$finder = New UserFinder();
		
		if ( $username != '' ) {
			$theuser = $finder->FindByName( $username );
			if ( strtoupper( substr( $username, 0, 1 ) ) == substr( $username, 0, 1 ) ) {
				$page->SetTitle( $username . " Ημερολόγιο" );
			}
			else {
				$page->SetTitle( $username . " ημερολόγιο" );
			}
		}
		if ( !isset( $theuser ) || $theuser === false ) {
			return Element( '404' );
		}
		$finder = New JournalFinder();
		$journals = $finder->FindByUser( $theuser );
		
		Element( 'user/sections' , 'journal' , $theuser );
		?><div id="journallist">
			<ul><?php
			foreach ( $polls as $poll ) {
				?><li><?php
					Element( 'journal/small' );
					?><div class="barfade">
						<div class="leftbar"></div>
						<div class="rightbar"></div>
					</div>
				</li><?php
			}
			?></ul>
		</div><img src="<?php
		echo $rabbit_settings[ 'imagesurl' ];
		?>heart.png" style="display:none;" /><?php
	}
?>
