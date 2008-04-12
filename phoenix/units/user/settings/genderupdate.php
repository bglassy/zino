<?php

	function UnitUserSettingsGenderupdate( tString $gender , tString $sex , tString $religion , tString $politics ) {
		$gender = $gender->Get();
		$sex = $sex->Get();
		$religion = $religion->Get();
		$politics = $politics->Get();
		?>$( '#sex' ).html( <?php
		    ob_start();
    		Element( 'user/settings/personal/sex' , $sex , $gender );
    		echo w_json_encode( ob_get_clean() );
		?> );
		$( '#sex select' ).change( function() {
			Settings.Enequeue( 'sex' , this.value );
		});
		$( '#religion' ).html( <?php
		    ob_start();
    		Element( 'user/settings/personal/religion' , $religion , $gender );
    		echo w_json_encode( ob_get_clean() );
		?> );
		$( '#religion select' ).change( function() {
			Settings.Enequeue( 'religion' , this.value );
		});
		$( '#politics' ).html( <?php
		    ob_start();
    		Element( 'user/settings/personal/politics' , $politics , $gender );
    		echo w_json_encode( ob_get_clean() );
		?> );
		$( '#politics select' ).change( function() {
			Settings.Enequeue( 'politics' , this.value );
		});<?php
	}
?>
