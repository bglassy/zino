<?php
	
	function ElementAlbumSmall( $album , $creationmockup = false ) {
		global $water;
		
		if ( !$creationmockup ) {
			$commentsnum = $album->Numcomments;
			$photonum = $album->Numphotos;
			?><div class="album">
				<a href="?p=album&amp;id=<?php
				echo $album->Id;
				?>">
		        	<span class="albummain">
		        		<img src="http://static.zino.gr/phoenix/mockups/apartments.jpg" alt="<?php
						echo htmlspecialchars( $album->Name );
						?>" title="<?php
						echo htmlspecialchars( $album-Name );
						?>" />
		        	</span>
		            <span class="desc"><?php
					echo htmlspecialchars( $album->Name );
					?></span>
		        </a>
		        <dl><?php
					if ( $photonum > 0 ) {
			            ?><dt><img src="http://static.zino.gr/phoenix/imagegallery.png" alt="Φωτογραφίες" title="Φωτογραφίες" /></dt>
			            <dd><?php
						echo $photonum;
						?></dd><?php
					}
					if ( $commentsnum > 0 ) {
						?><dt><img src="http://static.zino.gr/phoenix/comment.png" alt="Σχόλια" title="Σχόλια" /></dt>
						<dd><?php
						echo $commentsnum;
						?></dd><?php
					}
		        ?></dl>
			</div><?php
		}
		else {
			?><div class="album createalbum">
				<a href="">
		        	<span class="albummain">
		        		<img src="http://static.zino.gr/phoenix/mockups/apartments.jpg" alt="Νέο album" title="Νέο album" />
		        	</span>
		        </a>
				<span class="desc">
					<input type="text" />
				</span>
			</div><?php
		}
	}
?>
