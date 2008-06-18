<?php

    function ElementPmFolderView( PMFolder $folder ) {
        global $water;
        
    	$finder = New PMFinder();
    	$messages = FindByFolder( $folder );
    	$water->Trace('Pmfinder::FindByFolder', $messages);
		
    	if ( !count( $messages ) ) {
			if ( $folder->Typeid == PMFOLDER_INBOX ) {
				?>Δεν έχεις εισερχόμενα μηνύματα.<?php
			}
			else if ( $folder->Typeid == PMFOLDER_OUTBOX ) {
				?>Δεν έχεις στείλει ακόμα κάποιο μήνυμα.<?php
			}
			else {
				?>Δεν έχεις μηνύματα σε αυτό το φάκελο.<br />
				Μετακίνησε κάποια μηνύματα με το ποντίκι σε αυτό το φάκελο για να τα μεταφέρεις εδώ.<?php
			}
            return;
    	}

        foreach ( $messages as $msg ) {
            Element( 'pm/view', $msg, $folder );
        }
    }
?>
