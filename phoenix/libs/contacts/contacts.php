<?php
    function GetContacts( $username, $pass ) {
        global $libs;
        
        $libs->Load( 'contacts/OpenInviter/openinviter' );  
        $provider = "hotmail";//<--TODO ADD provider argument 
        /*$parts = array();
        $parts = explode( '@', $username );
        if( count( $parts ) < 2 ) {
            return false;
        }
        
        $provider_parts = array();
        $provider_parts = explode( '.', $parts[ 1 ] );
        $provider = $provider_parts[ 0 ];*/
        
        $inviter = new OpenInviter();
        $inviter->getPlugins();
        $inviter->startPlugin( $provider );
        $state = $inviter->login( $username, $pass );
        if( $state == false ) {
            return false;//Problem login in
        }
        $contacts = $inviter->getMyContacts();
        if( $contacts === false  ) {
            return false;//Problem accessing the contacs
        }
        $inviter->logout();
        $inviter->stopPlugin();
        
        $contact = new Contact();
        foreach ( $contacts as $key=>$val ) {
            $contact->AddContact( $key, $username );
            //EmailFriend( $key );
        }
        
        return true;
    }    
    
    function EmailFriend( $toemail ) {
            global $user;
            
            $parts = array();
            $parts = explode( '@', $toemail );
            $toname = $parts[ 0 ];            
            
            $subject = 'Πρόσκληση απο τον ' . $user->Name . ' στο Zino';
            //<>TODO
            $message = "Γεια σου $toname,

Ο/Η $user->Name σε πρόσθεσε στους φίλους του στο Zino. Γίνε μέλος στο Zino για να δεις τα προφίλ των φίλων σου, να φτιάξεις το δικό σου, και να μοιραστείς τις φωτογραφίες  και τα νέα σου.

Για να δεις το προφίλ του/της $user->Name στο Zino, πήγαινε στο:
http://$user->Name.zino.gr/

Ευχαριστούμε,
Η Ομάδα του Zino";
            $fromname = 'Zino';//<-TODO
            $fromemail = 'noreply@zino.gr';            //<-TODO
            Email( $toname, $toemail, $subject, $message, $fromname, $fromemail );
            return;
    }
    
    class ContactFinder extends Finder {
        protected $mModel = 'Contact';
        
        public function FindByUseridAndMail( $userid, $email ) {
            $prototype = new Contact();
            $prototype->Usermail = $email;
            $prototype->Userid = $userid;
            return $this->FindByPrototype( $prototype, 0, 10000 );
        }
        
        public function FindAllZinoMembersByUseridAndMail( $userid, $email ) {
            global $libs;            
            $libs->Load( "user/profile" );
        
            $all = $this->FindByUseridAndMail( $userid, $email );//Get all contacts tha the user added
            
            $all_emails = array();//Get members only mails
            foreach ( $all as $contact ) {
                $all_emails[] = $contact->Mail;
            }
            $mailfinder = new UserProfileFinder();
            $members = $mailfinder->FindAllUsersByEmails( $all_emails );
            return $members;
        }
    }
    
    class Contact extends Satori {
        protected $mDbTableAlias = 'contacts';
        
        public function AddContact( $mail, $usermail ) {
            global $user;
            $contact = new Contact();
            $contact->Mail = $mail;
            $contact->Usermail = $usermail;
            $contact->Userid = $user->Id;
            $contact->Created = NowDate();
            $contact->Save();
            return;
        }
    }
