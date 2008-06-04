<?php

    global $libs;
    $libs->Load( 'event' );

    class NotificationFinder extends Finder {
        protected $mModel = 'Notification';

        public function FindByUser( $user, $offset = 0, $limit = 20 ) {
            $notif = New Notification();
            $notif->Fromuserid = $user->Id;

            return $this->FindByPrototype( $notif, $offset, $limit, array( 'Id', 'DESC' ) );
        }
    }

    function Notification_FieldByEvent( $event ) {
        if ( $event->Typeid == EVENT_COMMENT_CREATED ) {
            $comment = $event->Item;
            if ( $comment->Parentid == 0 ) {
                return 'replies';
            }
            switch ( Type_FromObject( $comment->Item ) ) {
                case TYPE_JOURNAL:
                    return 'journals';
                case TYPE_IMAGE:
                    return 'photos';
                case TYPE_POLL:
                    return 'polls';
                case TYPE_USERPROFILE:
                    return 'profiles';
            }
        }
        else if ( $event->Typeid == EVENT_FRIENDRELATION_CREATED ) {
            return 'friends';
        }

        throw New Exception( 'Invalid event on Notification_FieldByEvent' );
    }

    class Notification extends Satori {
        protected $mDbTableAlias = 'notify';

        public function GetItem() {
            return $this->Event->Item;
        }
        public function GetFromUser() {
            return $this->Event->User;
        }
        public function Email() {
            global $rabbit_settings;

            switch ( $this->Event->Typeid ) {
                case EVENT_COMMENT_CREATED:
                    $target = 'notification/email/comment';
                    break;
                case EVENT_FRIENDRELATION_CREATED:
                    $target = 'notification/email/friend';
            }

            ob_start();
            $subject = Element( $target, $this );
            $message = ob_get_clean();

            // send an email
            mail( $this->ToUser->Email, $subject, $message, 'From: ' . $rabbit_settings[ 'applicationname' ] . ' <noreply@' . $rabbit_settings[ 'hostname' ] . ">\r\nReply-to: noreply <noreply@" . $rabbit_settings[ 'hostname' ] . '>' );
        }
        public function OnBeforeCreate() {
            global $water;
            $field = Notification_FieldByEvent( $this->Event );

            $attribute = 'Email' . $field;
            if ( $this->ToUser->Preferences->$attribute == 'yes' && !empty( $this->ToUser->Email ) && $this->ToUser->Emailverified ) {
                $this->Email();
            }
            
            $attribute = 'Notify' . $field;
            $water->Trace( "Notify attribute", $attribute );
            if ( $this->ToUser->Preferences->$attribute != 'yes' ) {
                $water->Trace( "No notification for user " . $this->ToUser->Name, $this->ToUser->Preferences->$attribute );
                if ( !is_object( $this->ToUser ) ) {
                    die( "touser not an object" );
                }
                if ( !is_object( $this->ToUser->Preferences ) ) {
                    die( "prefernces not an object" );
                }
                return false;
            }
            $water->Trace( "New notification for user " . $this->ToUser->Name, $this->ToUser->Preferences->$attribute );

            return true;
        }
        public function Relations() {
            $this->ToUser = $this->HasOne( 'User', 'Touserid' );
            $this->FromUser = $this->HasOne( 'User', 'Fromuserid' );
            $this->Event = $this->HasOne( 'Event', 'Eventid' );
        }
        public function OnBeforeUpdate() {
            throw New Exception( 'Notifications cannot be edited!' );
        }
        public function LoadDefaults() {
            $this->Created = NowDate();
        }
		public function GetSince() {
			return dateDiff( $this->Created, NowDate() );
		}
    }

?>
