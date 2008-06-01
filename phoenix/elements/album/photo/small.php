<?php
	function ElementAlbumPhotoSmall( $image , $showdesc = false ,  $showcomnum = false ) {
		if ( $image->Name != '' ) {
			$title = htmlspecialchars( $image->Name );
		}	
		else {
			$title = htmlspecialchars( $image->Album->Name );
		}
		?><div class="photo">
			<a href="?p=photo&amp;id=<?php
			echo $image->Id;
			?>"><?php
				Element( 'image', $image, IMAGE_PROPORTIONAL_210x210, '' , $title , $title , '' , false , 0 , 0 ); 
				if ( $showdesc && $image->Name != '') {
					?><br /><?php
					echo htmlspecialchars( $image->Name );
				}
			?></a><?php
			if (  $showcommnum ) {
				?><div><?php
					if ( $image->Numcomments > 0 ) {
						?><span class="commentsnum"><?php
						echo $image->Numcomments;
						?></span><?php
					}
				?></div><?php
			}
		?></div><?php
	}
?>
