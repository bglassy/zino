<?php
    class ControllerImagetag {
        public static function Create( $photoid, $personid, $top, $left, $width, $height ) {
			clude( "models/db.php" );
			clude( "models/user.php" );
			clude( "models/photo.php" );
			clude( "models/friend.php" );
			clude( "models/imagetag.php" );
			clude( "models/notification.php" );
			$top = ( int )$top;
			$left = ( int )$left;
			$width = ( int )$width;
			$height = ( int )$height;
			if ( !isset( $_SESSION[ 'user' ] ) ) {				
				throw New Exception( "Imagetag::Create - You are not logged in" );
			}
			$ownerid = $_SESSION[ 'user' ][ 'id' ];
			$photo = Photo::Item( $photoid );
            if ( empty( $photo ) ) {
                throw Exception( 'Invalid photo' );
            }
			// check if user is owner of photo or friend of owner; you can't tag some unknown person's photos
			if ( $photo[ 'userid' ] != $ownerid 
             && Friend::Strength( $photo[ 'userid' ], $ownerid ) != FRIENDS_BOTH ) {
		         throw Exception( 'You are not related to the owner of the image' );
		    }
			// now check that the tagged person is the friend of the user; you can't tag who doesn't know you
			if ( Friend::Strength( $ownerid, $personid ) != FRIENDS_BOTH 
				&& $ownerid != $personid ) {
            	throw Exception( 'You are not related to the person you are going to tag' );
	        }
			$info = ImageTag::Create( $personid, $photoid, $ownerid, $top, $left, $width, $height );
            $id = $info[ 'id' ];
			if(  $ownerid != $personid ) {
				Notification::Create( $ownerid, $personid, EVENT_IMAGETAG_CREATED, $id );
			}
            Template( 'imagetag/create', compact( 'id', 'personid', 'photoid', 'ownerid', 'top', 'left', 'width', 'height' ) );
			return;
        }
        public static function Listing( $photoid ) {
            clude( 'models/db.php' );
            clude( 'models/album.php' );
            clude( 'models/user.php' );
            clude( 'models/types.php' );
			clude( 'models/imagetag.php' );

			$photoid = ( int )$photoid;
			$tags = ImageTag::ListByPhoto( $photoid );
            Template( 'imagetag/listing', compact( 'tags', 'photoid' ) );
        }
        public static function Delete( $phototagid ) {
            clude( 'models/db.php' );
			clude( 'models/imagetag.php' );

            $tag = ImageTag::Item( $phototagid );
            $userid = $_SESSION[ 'user' ][ 'id' ];
            if ( $userid != $tag[ 'ownerid' ] && $userid != $tag[ 'personid' ] && $userid != $tag[ 'image' ][ 'userid' ] ) {
                throw Exception( 'You are not allowed to delete this tag' );
            }
            ImageTag::Delete( $phototagid );
            Template( 'imagetag/deleted', compact( 'tag' ) );
        }
    }

?>
