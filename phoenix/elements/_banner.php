<?php
    
    class ElementBanner extends Element {
        public function Render() {
            global $page;
            global $user;
            global $rabbit_settings;
            
            ?>
           <div id="lbanner">
                <h1>
                    <a href="<?php
                    echo $rabbit_settings[ 'webaddress' ];
                    ?>">
                        <img src="http://static.zino.gr/phoenix/logo.png" />
                    </a>
                </h1>
           </div>
           <div id="rbanner">
           </div>
           <div id="mbanner">
                <div<?php
                if ( $user->Exists() ) {
                    ?>id="loggedinmenu"<?php   
                }
                ?>><?php
                    if ( $user->Exists() ) {
                        if ( $user->Avatar->Id > 0 ) {
                            Element( 'image/view' , $user->Avatar->Id , $user->Id , $user->Avatar->Width , $user->Avatar->Height , IMAGE_CROPPED_100x100 , 'banneravatar' , $user->Name , '' , true , 50 , 50 , 0 );

                        }
                        else {
                            ?><img src="http://static.zino.gr/phoenix/anonymous100.jpg" alt="<?php
                            echo htmlspecialchars( $user->Name );
                            ?>" title="<?php
                            echo htmlspecialchars( $user->Name );
                            ?>" class="banneravatar" />
                            <?php
                        }
                        ?><ul>
                            <li>
                            <a href="<?php
                            ob_start();
                            Element( 'user/url' , $user->Id , $user->Subdomain );
                            echo htmlspecialchars( ob_get_clean() );
                            ?>" class="bannerinlink">Προφίλ</a>
                            
                            </li>
                            <li>
                                <a href="settings" class="bannerinlink">Ρυθμίσεις</a>
                            </li>
                            <li>
                                <a href="messages" class="bannerinlink<?php
                                $unreadcount = $user->Count->Unreadpms;
                                if ( $unreadcount > 0 ) {
                                    ?> unread<?php
                                }
                                ?>"><?php
                                if ( $unreadcount > 0 ) {
                                    echo $unreadcount;
                                    ?> νέ<?php
                                    if( $unreadcount == 1 ) {
                                        ?>ο μήνυμα<?php  
                                    }
                                    else {
                                        ?>α μηνύματα<?php
                                    }
                                }
                                else {
                                    ?>Μηνύματα<?php
                                }
                                ?></a>
                            </li>
                            <li>
                                <form method="post" action="do/user/logout">
                                    <a href="#" class="bannerinlink" onclick="this.parentNode.submit();return false;">Έξοδος</a>
                                </form>
                            </li>
                        </ul><?php
                    }
                    else {
                        ?><form id="loginform" action="do/user/login" method="post">
                            <input type="text" name="username" value="ψευδώνυμο" />
                            <input type="password" name="password" value="κωδικός" />
                            <a id="loginbutton" class="wlink" href="#">Είσοδος</a>
                            <span>
                                ή <a href="join" class="wlink" onclick="document.getElementById( 'loginform' ).submit();return false;">Εγγραφή</a>
                            </span>
                        </form><?php
                    }
                ?></div>
           </div><?php
       }
    }
?>
