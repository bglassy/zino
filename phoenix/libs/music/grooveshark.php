<?php
	
	function CreateUUID(){
		$uid = "";
		for( $i = 0; $i < 8; ++$i ){
			$uid .= dechex( mt_rand  ( 0, 15  ) );
		}
		$uid .= "-";
		for( $i = 0; $i < 4; ++$i ){
			$uid .= dechex( mt_rand  ( 0, 15  ) );
		}
		$uid .= "-";
		for( $i = 0; $i < 4; ++$i ){
			$uid .= dechex( mt_rand  ( 0, 15  ) );
		}
		$uid .= "-";
		for( $i = 0; $i < 4; ++$i ){
			$uid .= dechex( mt_rand  ( 0, 15  ) );
		}
		$uid .= "-";
		for( $i = 0; $i < 12; ++$i ){
			$uid .= dechex( mt_rand  ( 0, 15  ) );
		}
		return strtoupper( $uid );
	}
	
	function GetSessionID(){
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "http://listen.grooveshark.com/" );
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		curl_close ($ch);
		$result = explode( "=", $result );
		$result = $result[ 1 ];
		$result = explode( ";", $result );	
		return $result[ 0 ]; //PHPSESSIONID
	}
	
	function GetToken( $session, $uid ){
		$secretKey = md5( $session );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "https://cowbell.grooveshark.com/service.php" );
		curl_setopt( $ch, CURLOPT_COOKIE, "PHPSESSID=$session" );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, '{"header":{"session":"' . $session . '","uuid":"' . $uid . '","client":"gslite","clientRevision":"20091027.09"},"parameters":{"secretKey":"' . $secretKey . '"},"method":"getCommunicationToken"}');
		curl_setopt( $ch,CURLOPT_HTTPHEADER, array( "Content-type: application/json" ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		curl_close ($ch);
		$result = explode( '"', $result );
		return $result[ count( $result ) - 2 ];
	}
	
	
	function SearchSong( $query ){
		$uuid = CreateUUID();
		$session = GetSessionID();
		$token = GetToken( $session, $uuid );
		
		$specialtoken = "a12345" . sha1( "getSearchResults:$token:theHumansAreDead:a12345");
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "http://cowbell.grooveshark.com/more.php?getSearchResults" );
		curl_setopt( $ch, CURLOPT_COOKIE, "PHPSESSID=$session" );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, '{"header":{"token":"' . $specialtoken . '","session":"' . $session . '","uuid":"' . $uuid . '","client":"gslite","clientRevision":"20091027.09"},"parameters":{"query":"' . $query . '","type":"Songs"},"method":"getSearchResults"}');
		curl_setopt( $ch,CURLOPT_HTTPHEADER, array( "Content-type: application/json" ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		curl_close ($ch);
		return $result;
	}
	
	function SetSong( $id ){
		$uuid = CreateUUID();
		$session = GetSessionID();
		$token = GetToken( $session, $uuid );
		
		$specialtoken = "a12345" . sha1( "createWidgetIDFromSongIDs:$token:theHumansAreDead:a12345");
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "http://cowbell.grooveshark.com/service.php?createWidgetIDFromSongIDs" );
		curl_setopt( $ch, CURLOPT_COOKIE, "PHPSESSID=$session" );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, '{"header":{"token":"' . $specialtoken . '","session":"' . $session . '","uuid":"' . $uuid . '","client":"gslite","clientRevision":"20091027.09"},"parameters":{"songIDs":[' . $id . ']},"method":"createWidgetIDFromSongIDs"}');
		curl_setopt( $ch,CURLOPT_HTTPHEADER, array( "Content-type: application/json" ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		curl_close ($ch);
		$result = json_decode( $result, true );
		
		$widgetID = $result[ "result" ][ "widgetID" ];
		
		//TODO
		//Take the $widgetID and save it to the user profile table
		return true;
	}
	
	function DeleteSong(){
		//TODO
		//Set the widgetID field of the current loggedin user to -1
		return true;
	}
?>