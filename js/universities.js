var Uni = {
	Create : function() {
		var unitext = document.getElementById( 'uniname' );
		var unilist = document.getElementById( 'unilist' );
		alert( unitext.value );
		if ( unitext.value != '' ) {
			var newuni = document.createElement( 'div' );
			alert( newuni );
			alert( unilist );
			newuni.appendChild( document.createTextNode( unitext.value ) );
			alert( newuni.firstChild );
			unilist.appendChild( newuni );
		}
		else {
			alert( '���� ��� ������ ����� �������������' );
		}	
	}
}