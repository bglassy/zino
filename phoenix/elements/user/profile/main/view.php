<?php
    class ElementUserProfileMainView extends Element {
        public function Render( $theuser, $commentid, $pageno ) {
            global $libs;
            global $user;
            global $water;
            global $xc_settings;
            global $page;
            global $rabbit_settings;
            
            $libs->Load( 'poll/poll' );
            $libs->Load( 'comment' );
            $libs->Load( 'relation/relation' );
            $libs->Load( 'journal/journal' );
            $libs->Load( 'user/count' );
            $libs->Load( 'album' );
            $libs->Load( 'rabbit/helpers/http' );
            
            if ( $user->HasPermission( PERMISSION_COMMENT_VIEW ) ) {
            // if ( $theuser->Profile->Numcomments > 0 ) { // duh, problem here!
                $finder = New CommentFinder();
                if ( $commentid == 0 ) {
                    $comments = $finder->FindByPage( $theuser, $pageno );
                    $total_pages = $comments[ 0 ];
                    $comments = $comments[ 1 ];
                }
                else {
                    $speccomment = New Comment( $commentid );
                    $comments = $finder->FindNear( $theuser, $speccomment );
                    if ( $comments === false ) {
                        ob_start();
                        Element( 'user/url', $theuser->Id, $theuser->Subdomain );
                        return Redirect( ob_get_clean() );
                    }
                    $total_pages = $comments[ 0 ];
                    $pageno = $comments[ 1 ];
                    $comments = $comments[ 2 ];
                    
                    $libs->Load( 'notify/notify' );
                    
                    $finder = New NotificationFinder();
                    $finder->DeleteByCommentAndUser( $speccomment, $user );
                    $water->Trace( 'speccoment is ' . $speccomment->Id );
                }
            // }
            }

            $finder = New PollFinder();
            $polls = $finder->FindByUser( $theuser , 0 , 1 );
            $finder = New JournalFinder();
            $journals = $finder->FindByUser( $theuser , 0 , 1 );
            $egoalbum = New Album( $theuser->Egoalbumid );
            if ( $egoalbum->Numphotos > 0 ) {
                $finder = New ImageFinder();
                $images = $finder->FindByAlbum( $egoalbum , 0 , 10 );
            }
            
            $showuploadavatar = $theuser->Id == $user->Id && $egoalbum->Numphotos == 0;

            $finder = New FriendRelationFinder();
            if ( $finder->IsFriend( $user, $theuser ) == FRIENDS_B_HAS_A ) {
                Element( 'user/profile/main/antisocial', $theuser );
            }
            
            Element( 'user/profile/main/tweeter', $theuser );
            
            ?><div id="easyphotoupload" class="modal">
                <h2>Ανέβασε μια φωτογραφία...</h2>
                <div class="modalcontent">
                    <span class="plswait">
                    <img src="<?php
                    echo $rabbit_settings[ 'imagesurl' ];
                    ?>ajax-loader.gif" />
                    Παρακαλώ περιμένετε...</span>
                </div>
            </div><?php
            
            ?><div class="main"><?php
                if ( $showuploadavatar ) {
                    ?><div class="ybubble">    
                        <div class="body">
                            <h3>Ανέβασε μια φωτογραφία σου</h3>
                            <div class="uploaddiv">
                                <?php
                                if ( UserBrowser() == "MSIE" ) {
                                    ?><iframe src="?p=upload&amp;albumid=<?php
                                        echo $user->Egoalbumid;
                                        ?>&amp;typeid=2&amp;color=ffda74" class="uploadframe" id="uploadframe" scrolling="no" frameborder="0">
                                      </iframe><?php
                                }
                                else {
                                    ?><object data="?p=upload&amp;albumid=<?php
                                    echo $user->Egoalbumid;
                                    ?>&amp;typeid=2&amp;color=ffda74" class="uploadframe" id="uploadframe" type="text/html">
                                    </object><?php
                                }
                         ?></div>
                        </div>
                        <i class="bl"></i>
                        <i class="br"></i>
                    </div><?php
                }
                if ( $egoalbum->Numphotos > 0 ) {
                    ?><div class="photos"><?php
                        if ( $egoalbum->Numphotos > 5 ) {
                            ?><div class="more"><a href="?p=album&amp;id=<?php
                            echo $egoalbum->Id;
                            ?>" class="button" title="Περισσότερες φωτογραφίες μου">&raquo;</a></div><?php
                        }
                        Element( 'user/profile/main/photos' , $images , $egoalbum , $theuser->Id );
                        ?></div><?php
                }
                ?>
                <div class="morealbums"><?php
                    if ( $theuser->Count->Albums > 1 ) {
                        ?><div class="viewalbums"><a href="<?php
                        Element( 'user/url' , $theuser->Id , $theuser->Subdomain );
                        ?>albums" class="button">Προβολή albums&raquo;</a></div><?php
                    }
                ?></div><?php
                $finder = New FriendRelationFinder();
                $friends = $finder->FindByUser( $theuser , 0 , 12 );  
                if ( !empty( $friends ) || ( $user->Id == $theuser->Id && $user->Count->Relations == 0 ) ) { 
                        if ( $user->Id == $theuser->Id && $user->Count->Relations == 0 ) {
                            $usernorel = true;
                        }
                        else {
                            $usernorel = false;
                        }
                        Element( 'user/profile/main/friends' , $friends , $theuser->Count->Relations , $theuser->Id , $theuser->Subdomain , $usernorel );
                    ?><div class="barfade">
                        <div class="leftbar"></div>
                        <div class="rightbar"></div>
                    </div><?php
                }
                if ( !empty( $polls ) || ( $user->Id == $theuser->Id && $user->Count->Polls == 0 ) ) {
                    ?><div class="lastpoll">
                        <h3>Δημοσκοπήσεις<?php
                        if ( $theuser->Count->Polls > 0 ) {
                            ?> <span>(<a href="<?php
                            Element( 'user/url' , $theuser->Id , $theuser->Subdomain );
                            ?>polls">προβολή όλων</a>)</span><?php
                        }
                        ?></h3><?php
                        if ( $user->Id == $theuser->Id && $user->Count->Polls == 0 ) {
                            ?><div class="nopolls">
                            Δεν έχεις καμία δημοσκόπηση. Κάνε click στο παρακάτω link για να μεταβείς στη σελίδα
                            με τις δημοσκοπήσεις και να δημιουργήσεις μια.
                            <div><a href="<?php
                            Element( 'user/url' , $theuser->Id , $theuser->Subdomain );
                            ?>polls">Δημοσκοπήσεις</a>
                            </div>
                            </div><?php
                        } 
                        else {
                            ?><div class="container"><?php
                            Element( 'poll/small' , $polls[ 0 ] , true );
                            ?></div><?php
                        }
                    ?></div><?php
                }
                Element( 'user/profile/main/questions' , $theuser );
                if ( !empty( $polls ) /*or not empty questions*/ ) {
                    ?><div class="barfade" style="margin-top:20px;clear:right">
                        <div class="leftbar"></div>
                        <div class="rightbar"></div>
                    </div><?php
                }
                ?><div style="clear:right"></div><?php
                if ( !empty( $journals ) || ( $user->Id == $theuser->Id && $user->Count->Journals == 0 ) ) {
                    if ( $user->Id == $theuser->Id && $user->Count->Journals == 0 ) {
                        Element( 'user/profile/main/lastjournal', false, $theuser, 0, 0, 0, true );
                    }
                    else {
                        Element( 'user/profile/main/lastjournal' , $journals[ 0 ] , $theuser , $journals[ 0 ]->Id , $journals[ 0 ]->Numcomments , $theuser->Count->Journals , false );
                    }
                }
                if ( $user->HasPermission( PERMISSION_COMMENT_VIEW ) ) {
                    ?><div class="comments">
                        <h3>Σχόλια στο προφίλ <?php
                        if ( $theuser->Gender == 'f' ) {
                            ?>της <?php
                        }
                        else {
                            ?>του <?php
                        }
                        Element( 'user/name' , $theuser->Id , $theuser->Name , $theuser->Subdomain , false );
                        ?></h3><?php
                        if ( $pageno <= 0 ) {
                            $pageno = 1;
                        }
                        
                            if ( $user->HasPermission( PERMISSION_COMMENT_CREATE ) ) {
                                Element( 'comment/reply', $theuser->Id, TYPE_USERPROFILE , $user->Id , $user->Avatarid );
                            }
                        // if ( $theuser->Profile->Numcomments > 0 ) {
                            $indentation = Element( 'comment/list' , $comments , TYPE_USERPROFILE , $theuser->Id );
                            $page->AttachInlineScript( 'Comments.nowdate = "' . NowDate() . '";' );
                            $page->AttachInlineScript( "Comments.OnLoad();" );
                            if ( $commentid > 0 && isset( $indentation[ $commentid ] ) ) {
                                Element( 'comment/focus', $commentid, $indentation[ $commentid ] );
                            }
                            ?><div class="pagifycomments"><?php
                                $link = str_replace( '*', urlencode( $theuser->Subdomain ), $xc_settings[ 'usersubdomains' ] ) . '?pageno=';
                                Element( 'pagify' , $pageno , $link, $total_pages );
                            ?></div><?php
                        // }
                    ?></div><?php
                }
            ?></div><?php    
        }
    }
?>
