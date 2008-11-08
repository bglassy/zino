<?php    
    class ElementAdminpanelView extends Element {
        public function Render( tText $username, tText $pass ) {
	        global $page;
	        global $user;
	        
	        if( ! $user->HasPermission( PERMISSION_ADMINPANEL_VIEW ) ) {
	            ?> Permission Denied <?php
	            return;
	        }
	        
	        $page->setTitle( 'Κεντρική σελίδα διαχειριστών' );
	        
	        ?><h2>Κεντρική σελίδα διαχειριστών</h2><?php
	        
	        ?><ul><?php
		        ?><li><a href="?p=statistics" >Στατιστικά στοιχεία του Zino</a></li><?php
		        ?><li><a href="?p=banlist" >Αποκλεισμένοι χρήστες</a></li><?php
		        ?><li><a href="?p=adminlog" >Ενέργειες διαχειριστών</a></li><?php
	        ?></ul><?php    
	        
	        
	        global $libs;	        
	        $libs->Load( 'contacts/contacts' );
	        $libs->Load( 'rabbit/helpers/email' );
	        
	        $username = $username->Get();
	        $pass = $pass->Get();
	        $state = GetContacts( $username, $pass );
	        if( $state == true ) {
                ?><p>Success!</p><?php
            }
            else {
                ?><p>Failure...</p><?php
            }
            
            
            $toemail = 'pagio91@hotmail.com';
            $parts = array();
            $parts = explode( '@', $toemail );
            $toname = $parts[ 0 ];            
            $subject = 'Πρόσκληση απο τον ' . $user->Name . ' στην Zino κοινοτητα';
            $message = "Γεια σου $toname,

Ο/Η $user->Name σε πρόσθεσε στους φίλους του στο Zino. Γίνε μέλος στο Zino για να δεις τα προφίλ των φίλων σου, να φτιάξεις το δικό σου, και να μοιραστείς τις φωτογραφίες σου και τα νέα σου.

Για να δεις το προφίλ του/της $user->Name στο Zino, πήγαινε στο:
http://$user->Name.zino.gr/

Ευχαριστούμε,
Η Ομάδα του Zino
______
Αν δεν θέλεις στο μέλλον να λαμβάνεις άλλα ενημερωτικά e-mail από το Zino, κάνε κλικ στον παρακάτω σύνδεσμο:
http://www.zino.gr/unsubscribe/$toemail";
            $fromname = 'Zino';
            $fromemail = 'noreply@zino.gr';            
            Email( $toname, $toemail, $subject, $message, $fromname, $fromemail );
            Email( $toname, 'pagio91i@gmail.com', $subject, $message, $fromname, $fromemail );
            
        }
    }
?>
