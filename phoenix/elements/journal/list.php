<?php
	
	function ElementJournalList( tString $username ) {
		global $page;
		global $rabbit_settings;
		global $user;
		
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
				if ( $theuser->Id == $user->Id ) {
					?><li class="create">
						<a href="" class="new"><img src="<?php
						echo $rabbit_settings[ 'imagesurl' ];
						?>add3.png" alt="Δημιουργία δημοσκόπησης" title="Δημιουργία δημοσκόπησης" />Δημιουργία δημοσκόπησης</a>
					</li><?php
				}
				if ( !empty( $journals ) ) {
					foreach ( $journals as $journal ) {
						?><li><?php
							Element( 'journal/small' , $journal );
							?><div class="barfade">
								<div class="leftbar"></div>
								<div class="rightbar"></div>
							</div>
						</li><?php
					}
				}
				else {
					if ( $theuser->Id != $user->Id ) {
						?>Δεν υπάρχουν καταχωρήσεις<?php
					}
				}
			?></ul>
		</div>
		<div class="eof"></div><img src="<?php
		echo $rabbit_settings[ 'imagesurl' ];
		?>heart.png" style="display:none;" /><?php
	}
?>
