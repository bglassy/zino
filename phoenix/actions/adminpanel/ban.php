<?php
    function ActionAdminpanelBan( tText $username, tText $reason, tText $time_banned, tText $delete_journals ) {
        global $libs;
        
        $username = $username->Get();
        $reason = $reason->Get();
        $time_banned = $time_banned->Get();
        $delete_journals = $delete_journals->Get();
        
        if ( $reason == "" ) {
            $reason = "Δεν αναφέρθηκε";
        }
        
        $time_banned = $time_banned*24*60*60;//<--make days to secs
        
        $libs->Load( 'adminpanel/ban' );
        $libs->Load( 'journal' );
        $libs->Load( 'user/user' );
        
        $userfinder = new UserFinder();
        $user2ban = $userfinder->FindByName( $username );
        return Redirect( '?p=banlist&'.$user2ban ); 
       
        $ban = new Ban();
        $res = $ban->BanUser( $username, $reason, $time_banned );
         
        if ( $delete_journals == "yes" ) {
            $jFinder = new JournalFinder();
        }
           
        return Redirect( '?p=banlist' );
    }
?>
