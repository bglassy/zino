var advertise = {
	SendEmail : function() {
		var body = document.getElementById( 'body' );
		var inputlist = body.getElementsByTagName( 'input' );
		var mailadress = inputlist[ 0 ];
		var textarealist = document.getElementsByTagName( 'textarea' );
		var mailtext = textarealist[ 0 ];
		var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if ( filter.test( mailadress.value ) ) {
			if ( mailtext.value != '' ) {
			
			
			}
			else {
				var wrongmailtext = document.createElement( 'div' );
				wrongmailtext.appendChild( document.createTextNode( 'Παρακαλώ συμπληρώστε το πεδίο των σχολίων' ) );
				body.insertBefore( wrongmailtext , mailtext.nextSibling );
				Animations.Create( wrongmailtext , 'opacity' , 15000 , 1 , 0 , function() {
					wrongmailtext.parentNode.removeChild( wrongmailtext );
				} );
			}
		}
		else {
			var wrongmailspan = document.createElement( 'span' );
			wrongmailspan.style.paddingLeft = '20px';
			wrongmailspan.appendChild( document.createTextNode( 'Παρακαλώ δώστε ένα έγκυρο email' ) );
			body.insertBefore( wrongmailspan , mailadress.nextSibling );
			Animations.Create( wrongmailspan , 'opacity' , 15000 , 1 , 0 , function() {
				wrongmailspan.parentNode.removeChild( wrongmailspan );
			} );
			mailadress.focus();
			mailadress.select();
		}
		//alert( 'email is :' + mailadress );
		//alert( 'emailtext is :' + mailtext );	
	}
}