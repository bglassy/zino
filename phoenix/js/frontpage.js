var Frontpage = {
	Closenewuser : function ( node ) {
		$( 'div.frontpage div.ybubble' ).animate( { height : '0'} , 800 , function() {
			$( this ).remove();
		} );
	},
	/*
	Showunis : function( node ) {
		var divlist = node.getElementsByTagName( 'div' );
		var contenthtml = "<span style=\"padding-left:5px;\">ÐáíåðéóôÞìéï:</span><select><option value=\"0\" selected=\"selected\">-</option><option value=\"2\">Öéëïëïãßá</option><option value=\"6\">Çëåêôñïëüãùí Ìç÷áíéêþí &amp; Ìç÷áíéêþí Õðïëïãéóôþí</option><option value=\"9\">ÉáôñéêÞ</option><option value=\"23\">ÇëåêôñïíéêÞ</option><option value=\"25\">Öéëïóïößá</option><option value=\"43\">Èåïëïãßá</option><option value=\"35\">ÐëçñïöïñéêÞ</option><option value=\"67\">Ìç÷áíéêüò Õðïëïãéóôþí</option><option value=\"98\">ÏäïíôïúáôñéêÞ</option></select>";
		var newdiv = document.createElement( 'div' );
		newdiv.innerHTML = contenthtml;
		node.insertBefore( newdiv, divlist[ 0 ].nextSibling );
	},
	*/
	DeleteShout : function() {
	
	}
};
$( document ).ready( function() {
	if ( $( 'div.frontpage div.inshoutbox' )[ 0 ] ) {
		$( 'div.frontpage div.inshoutbox div.shoutbox div.comments div.newcomment div.bottom input' ).click( function() {
			alert( 'calling' );
			var list = $( 'div.frontpage div.inshoutbox div.shoutbox div.comments' );
			var text = $( list ).find( 'div.newcomment div.text textarea' )[ 0 ].value;
			if ( text == '' ) {
				alert( 'Δε μπορείς να δημοσιεύσεις κενό μήνυμα' );
			}
			else {
				var newshout = $( list ).find( 'div.empty' )[ 0 ].cloneNode( true );
				$( newshout ).show().find( 'div.text' ).append( document.createTextNode( text ) );
				$( newshout ).insertAfter( $( list ).find( 'div.newcomment' )[ 0 ] );
				$( $( list )[ list.length - 2 ] ).remove();
				
			}
			return false;
		} );
	}
} );