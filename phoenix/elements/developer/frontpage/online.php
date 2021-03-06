<?php
   class ElementDeveloperFrontpageOnline extends Element {
        public function Render() {
            global $libs;
            global $user;
            
            $libs->Load( 'user/user' );
	        $libs->Load( 'bennu/bennu' );
        
            $finder = New UserFinder();
		    $users = $finder->FindOnline( 0 , 70 );
            $count = $users->TotalCount();
            
            if ( UserIp() == ip2long( '85.75.181.28' ) ) {
               ?><!-- Time debug: <?php
                echo $xc_settings[ 'mysql2phpdate' ];
               ?>--><?php
            } 
            if ( $user->Exists() ) { //sort online users using bennu
                $target = $finder->FindById( $user->Id );       
                $users = Bennu_OnlineNow( $target, $users );
            }
            
            if ( $count ) {        
                ?><div class="onlineusers">
                    <h2<?php
                        if ( $count > 1 ) {
                            ?> title="<?php
                            echo $count;
                            ?> άτομα είναι online"<?php
                        }
                        ?>>Είναι online τώρα (<?php
                        echo $count;
                        ?>)</h2>
                        <ul class="lst ul2"><?php
                            foreach( $users as $onuser ) {
                                ?><li><a href="<?php
                                ob_start();
                                Element( 'developer/user/url', $onuser->Id , $onuser->Subdomain );
                                echo htmlspecialchars( ob_get_clean() );
                                ?>"><?php
                                Element( 'developer/user/avatar' , $onuser->Avatarid , $onuser->Id , $onuser->Avatar->Width , $onuser->Avatar->Height , $onuser->Name , 100 , 'nolazy' , '' , false , 0 , 0 );
                                ?></a></li><?php
                            }    
                        ?></ul><?php
                ?></div><?php
            }
        }
    }
?>
