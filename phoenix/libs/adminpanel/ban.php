<?php
    /*
        Developer:Pagio
    */
    
    class Ban {    
        public function Revoke( $userid ) {
            global $libs;
                        
            $libs->Load( 'adminpanel/bannedips' );
            $libs->Load( 'adminpanel/bannedusers' );    
            $libs->Load( 'user/user' );        
            
            $ipFinder = new BannedIpFinder();//delete related ips
            $ips = $ipFinder->FindByUserId( $userid );            
            
            foreach( $ips as $ip ) {
                $ip_d = new BannedIp( $ip->id );
                $ip_d->Delete();
            }
            
            $bannedUserFinder = new BanneduserFinder();//delete banneduser
            $bannedUsers = $bannedUserFinder->FindByUserId( $userid );            
            
            if ( !$bannedUsers ) {
                return;
            }
            else if ( count( $bannedUsers ) == 1 ) {
                $cur_user = current( $bannedUsers );
                $user_d = new BannedUser( $cur_user->id );
                $rights = $user_d->rights;
                $user_d->Delete();                
            }
                
            $userFinder = new UserFinder();//restore user rights
            $user = $userFinder->FindById( $userid );
            $user->rights = $rights;
            $user->Save();
              
            return;
        }
    
        public function isBannedIp( $ip ) {
               global $libs;     
               
               $libs->Load( 'adminpanel/bannedips' );
               
               $ipFinder = new BannedIpFinder();
               $res = $ipFinder->FindByIp( $ip );
               
               if ( !$res ) {               
                    return false;
               }
               else {                    
                    return true;
                }
        }
        
        public function isBannedUser( $userid ) {
            global $libs;
            
            $libs->Load( 'adminpanel/bannedusers' );
            
            $userFinder = new BannedUserFinder();
            $res = $userFinder( $userid );
            
            if ( !$res ) {
                return false;
            }
            else {
                $user = current( $res );
                $diff = strtotime( NowDate() ) - strtotime( $user->expire );
                if ( diff > 0 ) {
                    $this->Revoke( $user->id );
                    return false;
                }
                else {
                    return true;
                }
            }
        }
        
        public function BanUser( $user_name ) {
            global $libs;
            global $db;
            
            $libs->Load( 'user/user' );        
            $libs->Load( 'adminpanel/bannedips' );
            $libs->Load( 'adminpanel/bannedusers' );
            $libs->Load( 'loginattempt' );
            
            
            //check if the user doesn't exist or is already banned
            $userFinder = new UserFinder();
            $b_user = $userFinder->FindByName( $user_name );
            
            if ( !$b_user ) {
                return false;
            }
            
            $bannedUserFinder = new BannedUserFinder();
            $exists = $bannedUserFinder->FindByUserId( $b_user->id );
            
            if ( $exists ) {
                return false;
            }
            //
            
            //trace relevant ips from login attempts --implement as Finder
            $query = $db->Prepare( 
                'SELECT * FROM :loginattempts
                WHERE login_username=:username 
                GROUP BY  `login_ip`'
            );
            $query->BindTable( 'loginattempts' );
            $query->Bind( 'username' , $user_name );            
            $res = $query->Execute();            
            
            $logs = array();
            while( $row = $res->FetchArray() ) {
                $log = new LoginAttempt( $row );
                $logs[] = $log->ip;
            }
            //
            
            //ban this ips and ban user with this username
            $this->BanIps( $logs, $b_user );

            $banneduser = new BannedUser();
            $banneduser->userid = $b_user->id;
            $banneduser->rights = $b_user->rights;
            $banneduser->started = date( 'Y-m-d H:i:s', time() );
            $banneduser->expire = date( 'Y-m-d H:i:s', time() + 20*24*60*60 );
            $banneduser->delalbums = 0;            
            $banneduser->Save();
            
            $b_user->rights=0;
            $b_user->Save();
            //

            return true;
        }
        
        protected function BanIps( $ips, $b_user ) {
            global $libs;
            
            $libs->Load( 'adminpanel/bannedips' );
        
            $started = date( 'Y-m-d H:i:s', time() );
            $expire = date( 'Y-m-d H:i:s', time() + 20*24*60*60 );
            foreach( $ips as $ip ) {
                $banip = new BannedIp();
                $banip->ip = $ip;
                $banip->userid = $b_user->id;
                $banip->started = $started;
                $banip->expire = $expire;
                $banip->Save();
            }
            return;
        }
    }
?>
