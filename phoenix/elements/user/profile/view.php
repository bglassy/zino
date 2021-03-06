<?php
    class ElementUserProfileView extends Element {
        public function Render( tText $name , tText $subdomain, tInteger $commentid , tInteger $pageno ) {
            global $page;
            global $user;
            global $water;
            global $libs;

            $libs->Load( 'relation/relation' );
            $libs->Load( 'user/lastactive' );
            $libs->Load( 'user/profile' );
            $libs->Load( 'image/image' );

            $commentid = $commentid->Get();
            $pageno = $pageno->Get();
            $name = $name->Get();
            $subdomain = $subdomain->Get();
            $finder = New UserFinder();

            Element( 'user/subdomainmatch' );

            if ( $name != '' ) {
                $theuser = $finder->FindByName( $name );
            }
            else if ( $subdomain != '' ) {
                $theuser = $finder->FindBySubdomain( $subdomain );
            }
            if ( !isset( $theuser ) || $theuser === false ) {
                return Element( '404', 'Ο χρήστης δεν υπάρχει' );
            }

            if ( $theuser->Deleted ) {
                $libs->Load( 'rabbit/helpers/http' );
                return Redirect( 'http://static.zino.gr/phoenix/deleted' );
            }
            if ( Ban::isBannedUser( $theuser->Id ) ) {
                $libs->Load( 'rabbit/helpers/http' );
                return Redirect( 'http://static.zino.gr/phoenix/banned' );
            }
            
            $page->SetTitle( $theuser->Name );
            $page->AddKeyword( $theuser->Name );

            if ( $user->Id != $theuser->Id && $user->Exists() ) {
                $finder = New FriendRelationFinder();
                $res = $finder->FindFriendship( $user, $theuser );
                if ( $res === false ) {
                    $isfriend = 'false';
                }
                else {
                    $isfriend = 'true';
                }
                $page->AttachInlineScript( 'Profile.ShowFriendLinks( ' . $isfriend . ' , " ' .$theuser->Id . ' " );' );
            }
			$onload = '';
            if ( $user->Id == $theuser->Id ) {
				if ( $user->Betastatus == 2 ) {
					$user->Betastatus = 1;
					$user->Save();
					$user->Profile->Save(); // force user profile uncache
					
					$libs->Load( 'rabbit/helpers/http' );
					
					return Redirect( 'http://static.zino.gr/phoenix/udat/beta.html' );
				}
				$onload = 'Profile.MyProfileOnLoad();';
            }
			$onload .= 'Profile.OnLoad( "' . $theuser->Name . '" );';
            $page->AttachInlineScript( '$( function () { ' . $onload . ' } );' );
			
            if ( $theuser->LastActivity->Updated != '0000-00-00 00:00:00' ) {
                if ( $theuser->LastActivity->IsOnline() ) {
                    $text = "αυτή τη στιγμή!";
                }
                else {
                    ob_start();
                    Element( 'date/diff' , $theuser->LastActivity->Updated );
                    $text = ob_get_clean();
                }
                $page->AttachInlineScript( 'Profile.ShowOnlineSince( " ' . $text . ' " );' );
            }
            else {
                $page->AttachInlineScript( 'Profile.ShowOnlineSince( false );' );
            }
            $dob = explode( '-', $theuser->Profile->Dob );
            if ( count( $dob ) == 3 && $dob[ 0 ] != '0000' ) {
                $page->AttachInlineScript( 'Profile.CheckBirthday( ' . $dob[ 0 ] . ', ' . $dob[ 1 ] . ', ' . $dob[ 2 ] . ' );' );
                $page->AttachInlineScript( "Profile.FetchContacts( '$subdomain' );" );
            }
            ?><div id="profile"><?php
                $schoolexists = $theuser->Profile->School->Numstudents > 2;
                Element( 'user/profile/sidebar/view' , $theuser , $theuser->Id , $theuser->Profile->Updated, $schoolexists );
                $e = Element( 'user/profile/main/view' , $theuser, $commentid, $pageno );
                if ( $e instanceof HTTPRedirection ) {
                    return $e;
                }
                ?><div class="eof"></div>
            </div><?php
        }
    }
?>
