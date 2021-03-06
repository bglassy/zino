<?php
    class ElementApplicationList extends Element {
    
        public function Render() {
            global $libs;
            global $user;
            global $page;
            global $rabbit_settings;
            
            $libs->Load( 'application' );
            
            $testers = Array(
                                18, //feedWARd
                                3190, //darklord
                                11637, //mariosal
                                4060, //gspan
                                11812, //FakeDrake
                                5647, //nikos89
                                5176, //teh-ninja
                            );
            
            if ( !$user->Exists() && !( in_array( $user->Id, $testers ) ||
                 $user->HasPermission( PERMISSION_ADMINPANEL_VIEW ) ||
                 !$rabbit_settings[ 'production' ] ) )
            {
                ?>Δεν έχεις πρόσβαση σε αυτήν την σελίδα.<?
                return;
            }
            
            ?><div id="appmanager">
                <ul id="applist"><?php
                $finder = New ApplicationFinder();
                $apps = $finder->FindByUser( $user );
                if ( $apps ) {
                    foreach ( $apps as $app ) {
                        Element( 'application/view', $app );
                    }
                }
                else {
                    ?>Δεν έχεις καταχωρήσει καμία εφαρμογή.<?php
                }
                ?><li><div class="appbubble" id="newappbubble"><form method="POST" action="do/application/create">
                    <span id="edittitle"><h3>Δημιουργία</h3></span>
                    <table border="0" padding="0">
                        <tr><td>Όνομα:</td><td><input type="text" name="name" /></td></tr>
                        <tr><td>Διέυθυνση:</td><td><input type="text" name="url" /></td></tr>
                        <tr><td>Περιγραφή:</td><td><input type="textarea" name="description" /></td></tr>
                        <tr><td>Λογότυπο:</td><td><input type="text" name="logo" /></td></tr>
                        </table>
                        <br />
                        Η επεξεργασία των εφαρμογών θα είναι διαθέσιμη σε λίγες μέρες
                        <input type="submit" name="newapplication" onclick="Applications.CheckValidity()" value="Δημιουργία" />
                    </form></div></li>
                </ul>
                <a id="newapplink" href="">Νέα εφαρμογή</a>
            </div>
            <?php
            $page->AttachInlineScript( "Applications.OnLoad();" );
        }
    }
?>