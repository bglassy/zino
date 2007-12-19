<?php
	function ElementAdminMakesubdomains( ) {
		global $user;
        global $db;
		global $users;
		
		if ( $user->Username() != 'makis' ) {
			?>Δεν έχετε πρόσβαση<?php
			return 0;
		}
		
		$sql = "SELECT 
					`user_id` , `user_name` 
				FROM 
					`$users` 
				WHERE 
					`user_subdomain` = ''
				LIMIT 30 ;";
		
        $res = $db->Query( $sql );
        
        $rows = array();
		$subdomains = array();
		?><h2>Subdomains</h2>
		<table><?php
        while ( $row = $res->FetchArray() ) {
			$subdomains[ $row[ 'user_id' ] ] = User_DeriveSubdomain( $row[ 'user_name' ] );
            ?><tr><td><?php echo htmlspecialchars( $row[ 'user_id' ] ); ?>: <?php echo htmlspecialchars( $row[ 'user_name' ] ); ?></td><td><?php echo $subdomains[ $row[ 'user_id' ] ]; ?></td></tr><?php
        }
		?></table><br /><?php
		// CHECKING FOR DUPLICATES
		
		// 1) in the array we've already got
		$diff = array_diff( $subdomains, array_unique( $subdomains ) );
		echo htmlspecialchars( $diff ); ?><br /><?php
		// 2) in the rest of the database
		$list = htmlspecialchars( implode( "', '", array_values( $subdomains ) ) ); 
		echo "IN ( '$list' )";

		$sql = "SELECT 
					`user_id` , `user_name` , `user_subdomain`
				FROM 
					`$users` 
				WHERE 
					`user_subdomain` IN ( '$list' ) 
				LIMIT 1;";
		$sqlresult = $db->Query( $sql );
		if ( $sqlresult->Results() ) { // If there is someone in the list with the same subdomain
			$conflict = $sqlresult->FetchArray();
			echo "Too bad. At least user " . $conflict[ 'user_id' ] . ": " . htmlspecialchars( $conflict[ 'user_name' ] ) . " with subdomain " . $conflict[ 'user_subdomain' ] . " conflicts with one of the above list.";
			return 2;
		}

		?><br />--<?php
		
	}
/* -- samples --
"UPDATE `ccbeta`.`merlin_users` SET `user_subdomain` = '$subdomain' WHERE `merlin_users`.`user_id` =$userid LIMIT 1 ;"

"SELECT `user_id` , `user_name` 
FROM `merlin_users` 
WHERE `user_subdomain` = ''
LIMIT 30 ; "
*/
?>