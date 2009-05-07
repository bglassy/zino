<?php
    function UnitContactsRetrieve( tText $provider , tText $username, tText $password ) {
        global $libs;
        global $user;
        $provider = $provider->Get();
        $username = $username->Get();
        $password = $password->Get();
        
        $libs->Load( 'contacts/contacts' );
        $ret = GetContacts( $username, $password, $provider );
        
        echo "alert( '";
        foreach ( $ret as $name => $content ){
            echo $name . " => " . $contact . "<br />";
        }
        echo "' );";
        
        if( !is_array( $ret ) ){
            ?>
            setTimeout( function(){
            <?php
                if ( $ret == 'ERROR_PROVIDER' ){
                    ?>$( "#security" ).css({
                        'background': '#FF9090 url(http://static.zino.gr/phoenix/error.png) no-repeat 6px center',
                        'font-weight': 'bold',
                        'padding': '10px 10px 10px 30px'
                    }).html( 'Υπήρξε πρόβλημα στο σύστημα. Παρακαλώ δοκιμάστε αργότερα.' );<?php
                }
                if ( $ret == 'ERROR_CREDENTIALS' ){
                    ?>$( "#security" ).css({
                        'background': '#FEF4B7 url(http://static.zino.gr/phoenix/warning.png) no-repeat 6px center',
                        'font-weight': 'bold',
                        'padding': '10px 10px 10px 30px'
                    }).html( 'Το e-mail ή ο κωδικός που έγραψες δεν είναι σωστά.' );<?php
                }
            ?>
                contacts.backToLogin();
            }, 3000 );<?php
            return;
        }
        $contactsInZino = 0;
        $contactsNotZino = 0;
        $mailfinder = new UserProfileFinder();
        $members = $mailfinder->FindAllUsersByEmails( $ret );
        foreach( $ret as $mail ){
            if ( $members[ $mail ] != "" ){
                $theuser = new User( $members[ $mail ] );
                ?>contacts.addContactInZino( '<?php
                Element( 'user/display', $theuser->Id, $theuser->Avatar->Id, $theuser );
                ?>', '<?php
                echo addslashes( $mail );
                ?>' );
                <?php
                $contactsInZino++;
            }
            else {
                ?>contacts.addContactNotZino( '<?php
                echo addslashes( $mail );
                ?>' );<?php
                $contactsNotZino++;
            }
        }
        ?>$( "#contactsInZino > h3" ).html( "<?php
            echo $contactsInZino;
            if ( $contactsInZino == 1 ){
                ?> επαφή σου έχει Zino. Πρόσθεσέ την στους φίλους σου...<?php
            }
            else{
                ?> επαφές σου έχουν Zino. Πρόσθεσέ τις στους φίλους σου...<?
            }
        ?>" );
        $( "#contactsNotZino > h3" ).html( "<?php
            echo $contactsNotZino;
            if ( $contactsNotZino == 1 ){
                ?> επαφή σου δεν έχει Zino ακόμα! Προσκάλεσέ την τώρα!<?php
            }
            else{
                ?> επαφές σου δεν έχουν Zino ακόμα! Προσκάλεσέ τους τώρα!<?
            }
        ?>" );
        setTimeout( function(){
                contacts.previwContacts<?php
                if ( !$contactsInZino ){
                echo "Not";
                }
                ?>InZino();
            }, 3000 );<?php
    }
?>
