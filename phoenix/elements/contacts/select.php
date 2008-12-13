<?php
    class ElementContactsSelect extends Element {
        public function Render( tText $email, tInteger $step ) {
            global $page;
            global $user;
            global $libs;
            
            $libs->Load( 'contacts/contacts' );
            $libs->Load( 'user/profile' );
            
            $page->SetTitle( "Επιλογή Επαφών" );
            
            $email = $email->Get();
            $step = $step->Get();            
            if ( $step != 1 && $step != 2 ) {
                $step = 0;
            }
            
            $finder = new ContactFinder();
            $res = $finder->FindByUseridAndMail( $user->Id, $email );
            
            if( count( $res ) == 0 ) {
                ?><p>Παρουσιάστηκε κάποιο πρόβλημα.</p><?php
                return;
            }
            
            $all_emails = array();
            foreach ( $res as $sample ) {
                $all_emails[] = $sample->Mail;
            }
            $mailfinder = new UserProfileFinder();
            $zino_emails = $mailfinder->FindAllUsersByEmails( $all_emails );
            
            if ( $step == 1 ) { //step 1:send invites to user that are already in zino
                $friendsN = count( $zino_emails );
                if ( $friendsN == 0 ) {
                    ?><h3>Κανένας φίλος σου δεν είναι μέλος στο zino.Πήγαινε στο επόμενο βήμα για να τους προσκαλέσεις!</h3><?php
                    ?><input type="submit" value="Επόμενο βήμα" /><?php
                }
                else {            
                    ?><h3>Αυτοί οι φίλοι σου είναι ήδη στο zino!</h3><?php
                    ?><form method="post" action=""><?php
                    //echo count( $zino_emails );
                    foreach ( $zino_emails as $key=>$val ) {
                        ?><p><?php
                        ?><input type="checkbox" name="approved" /> <?php 
                        $friend = new User( $val );                              
                        echo $friend->Name . " - " . $friend->Profile->Email;                    
                        ?></p><?php
                    }
                    ?><input type="submit" value="Στείλε τις προσκλήσεις!" /><?php
                    ?></form><?php
                }
            }
            
            if ( $step == 2 ) { //step 2 - send invites to non zino users      
                ?><h3>Στείλε προσκλήσεις στους φίλους σου που δεν είναι μέλη στο Ζινο.</h3><?php
                ?><form method="post" action="do/contacts/invite"><?php
                //echo count( $res );
                foreach ( $res as $sample ) {
                    if ( $zino_emails[ $sample->Mail ] == NULL  ) {
                        ?><p><?php
                        ?><input type="checkbox" name="approved" /> <?php 
                        echo $sample->Mail;                        
                        ?></p><?php
                    }
                }
                ?><input type="submit" value="Στείλε τις προσκλήσεις!" /><?php
                ?></form><?php
            }
            return;
        }
    }
?>
