<?php
    /*
        Developer: Dionyziz
    */

    class UserException extends Exception {
    }

    global $libs;

    $libs->Load( 'user/permission' );
    $libs->Load( 'user/settings' );
    $libs->Load( 'user/profile' );
    $libs->Load( 'user/space' );
    $libs->Load( 'user/lastactive' );
    $libs->Load( 'user/count' );
    $libs->Load( 'image/image' );
    $libs->Load( 'journal' );
    $libs->Load( 'album' );
    $libs->Load( 'mood' );
    
    function User_Valid( $username ) {
        return ( bool )preg_match( '#^[a-zA-Z][a-zA-Z\-_0-9]{3,49}$#', $username );
    }
	function User_DeriveSubdomain( $username ) {
		/* RFC 1034 - They must start with a letter, 
		end with a letter or digit,
		and have as interior characters only letters, digits, and hyphen.
		Labels must be 63 characters or less. */
		$username = strtolower( $username );
		$username = preg_replace( '/([^a-z0-9-])/i', '-', $username ); //convert invalid chars to hyphens
		$pattern = '/([a-z]+)([a-z0-9-]*)([a-z0-9]+)/i';
		if ( !preg_match( $pattern, $username, $matches ) ) {
			return false;
		}
		return $matches[ 0 ];
	}
    
    class UserFinder extends Finder {
        protected $mModel = 'User';
        
        public function FindAll() {
            return $this->FindByPrototype( New User() );
        }
        public function IsTaken( $username ) {
            if ( $this->FindByName( $username ) !== false ) {
                return true;
            }
            $subdomain = User_DeriveSubdomain( $username );
            if ( $subdomain === false ) {
                return true;
            }
            if ( $this->FindBySubdomain( $subdomain ) !== false ) {
                return true;
            }
            return false;
        }
        public function FindByNameAndPassword( $username, $password ) {
            $prototype = New User();
            $prototype->Name = $username;
            $prototype->Password = $password;
            return $this->FindByPrototype( $prototype );
        }
        public function FindByIdAndAuthtoken( $userid, $authtoken ) {
            $prototype = New User();
            $prototype->Id = $userid;
            $prototype->Authtoken = $authtoken;

            return $this->FindByPrototype( $prototype );
        }
        public function FindByName( $name ) {
            $prototype = New User();
            $prototype->Name = $name;
            return $this->FindByPrototype( $prototype );
        }
        public function FindBySubdomain( $subdomain ) {
            $prototype = New User();
            $prototype->Subdomain = $subdomain;
            return $this->FindByPrototype( $prototype );
        }
        public function FindLatest() {
            return $this->FindByPrototype( New User(), 0, 25, array('Created', 'DESC') );
        }
        public function FindOnline( $offset = 0, $limit = 100 ) {
            $query = $this->mDb->Prepare(
                'SELECT
                    :users.*
                FROM
                    :users CROSS JOIN :lastactive
                        ON `user_id` = `lastactive_userid`
                WHERE
                    `lastactive_updated` > NOW() - INTERVAL 5 MINUTE
                ORDER BY
                    `lastactive_updated` DESC
                LIMIT
                    :offset, :limit'
            );
            $query->BindTable( 'users' );
            $query->BindTable( 'lastactive' );
            $query->Bind( 'offset', $offset );
            $query->Bind( 'limit', $limit );
            
            return $this->FindBySQLResource( $query->Execute() );
        }
		public function Count() {
			$query = $this->mDb->Prepare(
				'SELECT
					COUNT(*) AS numusers
				FROM
					:users;'
			);
			$query->BindTable( 'users' );
			$res = $query->Execute();
			$row = $res->FetchArray();
			return ( int )$row[ 'numusers' ];
		}
        public function ClearPlace( $placeid ) {
            $query = $this->mDb->Prepare(
                'UPDATE
                    :users
                SET
                    `placeid` = 0
                WHERE
                    `placeid` = :placeid'
            );
            $query->BindTable( 'users' );
            $query->Bind( 'placeid', $placeid );
            $query->Execute();
        }
    }

    class User extends Satori {
        protected $mDbTableAlias = 'users';
       
        protected function SetPassword( $value ) {
            $this->Password = md5( $value );
        }
        protected function GetPassword() {
            throw New UserException( 'User passwords cannot be retrieved, as they are encrypted' );
        }
        protected function GetLastActive() {
            return $this->LastActivity->Updated;
        }
        protected function Relations() {
            $this->Preferences = $this->HasOne( 'UserSettings', 'Id' );
            $this->Profile = $this->HasOne( 'UserProfile', 'Id' );
            $this->Count = $this->HasOne( 'UserCount', 'Id' );
            $this->Journals = $this->HasMany( 'JournalFinder', 'FindByUser', $this );
            $this->Albums = $this->HasMany( 'AlbumFinder', 'FindByUser', $this );
            $this->Space = $this->HasOne( 'UserSpace', 'Id' );
            $this->LastActivity = $this->HasOne( 'UserLastActive', 'Id' );
            $this->EgoAlbum = $this->HasOne( 'Album', 'Egoalbumid' );
            $this->Avatar = $this->HasOne( 'Image', 'Icon' );
        }
        protected function OnBeforeDelete() {
            foreach ( $this->Albums as $album ) {
                $album->Delete();
            }
            foreach ( $this->Journals as $journal ) {
                $journal->Delete();
            }
            if ( $this->Profile->Exists() ) {
                $this->Profile->Delete();
            }
            if ( $this->Preferences->Exists() ) {
                $this->Preferences->Delete();
            }
        }
        public function HasPermission( $permission ) {
            return $this->Rights >= $permission;
        }
        protected function LoadDefaults() {
            $this->Rights = 30; // default permissions of user right after registering
            $this->Registerhost = UserIp();
            $this->RenewAuthtoken(); // create a basic authtoken
        }
        protected function AfterConstruct( /* ... */ ) {
            $args = func_get_args();
            if ( count( $args ) == 1 ) {
                if ( is_array( $args[ 0 ] ) ) {
                    if ( count( $args[ 0 ] ) == 0 ) {
                        // construction by empty array -- logged out user
                        $this->ConstructLoggedOut();
                    }
                }
            }
        }
        protected function ConstructLoggedOut() {
            $this->Rights = 10; // logged out permissions
        }
        public function UpdateLastLogin() {
            $this->Lastlogin = time();
        }
        protected function OnCreate() {
            global $water;
            
            $water->Trace( 'User OnCreate: $this->Profile->Userid = ' . $this->Profile->Userid );
            
            $this->Profile->Save();
            $this->Preferences->Save();
            $this->EgoAlbum->Userid = $this->Id;
            $this->EgoAlbum->Save();
            $this->Egoalbumid = $this->EgoAlbum->Id;
            $this->Save();
        }
        public function RenewAuthtoken() {
            // generate authtoken
            // first generate 16 random bytes
            // generate 8 pseurandom 2-byte sequences 
            // (that's bad but generally conventional pseudorandom generation algorithms do not allow very high limits
            // unless they repeatedly generate random numbers, so we'll have to go this way)
            $bytes = array(); // the array of all our 16 bytes
            for ( $i = 0; $i < 8 ; ++$i ) {
                $bytesequence = rand(0, 65535); // generate a 2-bytes sequence
                // split the two bytes
                // lower-order byte
                $a = $bytesequence & 255; // a will be 0...255
                // higher-order byte
                $b = $bytesequence >> 8; // b will also be 0...255
                // append the bytes
                $bytes[] = $a;
                $bytes[] = $b;
            }
            // now that we have 16 "random" bytes, create a string of 32 characters,
            // each of which will be a hex digit 0...f
            $authtoken = ''; // start with an empty string
            foreach ( $bytes as $byte ) {
                // each byte is two authtoken digits
                // split them up
                $first = $byte & 15; // this will be 0...15
                $second = $byte >> 4; // this will be 0...15 again
                // convert decimal to hex and append
                // order doesn't really matter, it's all random after all
                $authtoken .= dechex($first) . dechex($second);
            }
			
			$this->Authtoken = $authtoken;
        } 
        // user added a new comment; for profile comments, UserProfile::OnCommentCreate
        public function OnCommentCreate() {
            ++$this->Count->Comments;
            $this->Count->Save();
        }
        public function OnCommentDelete() {
            --$this->Count->Comments;
            $this->Count->Save();
        }
    }
?>
