var ColorPicker = {
	Create : function() {
		alert("Iparxo");
		var table = document.createElement( 'table' );
		table.border="0";
		table.cellpadding="0";
		table.cellspacing="0";
		for( var y=0;y<300;++y ) {
			var tr = document.createElement( 'tr' ); 
			for ( var i=0;i<600;i+=10 ) {
				var td = document.createElement( 'td' );
				td.style.backgroundColor = "#000000";
				
				var img = document.createElement( 'img' );
				img.src = "keno.png";
				img.height="2";
				img.width="2";
				
				td.appendChild( img );
				tr.appendChild( td );
			}
			table.appendChild( tr );
			alert("OK");
		}
		document.getElementById( 'test' ).appendChild(table);
		
	}
};
