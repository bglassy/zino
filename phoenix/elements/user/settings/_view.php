<?php
/*
	Masked by: Rhapsody
	Reason: new ajax loading tabs for settings testing
	
	STOP! was masked
*/
    class ElementUserSettingsView extends Element {
        public function Render() {
            global $user;
            global $rabbit_settings;
            global $page;
            global $libs;
            
            $libs->Load( 'user/settings' );
            
            $page->SetTitle( 'Ρυθμίσεις' );
            if ( !$user->Exists() ) {
                $libs->Load( 'rabbit/helpers/http' );
                return Redirect( $rabbit_settings[ 'webaddress' ] );
            }
            ?><div class="settings">
                <div class="sidebar"><?php
                    Element( 'user/settings/sidebar' );
                ?></div>
                <div class="tabs">
                </div>
				<div id="test" >
				
				</div>
            </div>
            <div class="eof"></div><?php
            $page->AttachInlineScript( 'Settings.SettingsOnLoad();' );
            //$page->AttachInlineScript( 'Suggest.OnLoad();' );
        }
    }
?>