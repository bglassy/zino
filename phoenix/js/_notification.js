var Notification = {
    Expanded : true,
    TraversedAll : false,
	Visit : function( url , typeid , eventid , commentid ) {
        //Notification.DecrementCount();
		if ( typeid == 3 ) {
			document.location.href = url;
		} 
		else {
			Coala.Warm( 'notification/delete' , { eventid : eventid , relationnotif : false } );
			document.location.href = url;
		}
		return false;
	},
	Delete : function( eventid ) {
        if ( Notification.INotifs === 0 && Notification.VNotifs <= 5 ) {
            --Notification.VNotifs;
        }
		$( '#event_' + eventid ).animate( { opacity : "0" , height : "0" } , 400 , "linear" , function() {
			$( this ).remove();
            if ( Notification.VNotifs === 0 ) {
                $( "div.notifications" ).remove();
            }
		} );
		Coala.Warm( 'notification/delete' , { eventid : eventid , relationnotif : false } );
        
        if ( Notification.INotifs > 0 ) {
            var newnotif = $( '#inotifs div.event:first-child' );
            var clonenew = $( newnotif ).clone( true );
            $( "div.notifications div.list" ).append( clonenew );
            clonenew = $( "div.notifications div.list div.event:last-child" )[ 0 ];
            var targetheight = clonenew.offsetHeight;
            $( clonenew ).css( {
                "height" : "0",
                "opacity" : "0"
            } )
            .animate( {
                "height" : targetheight,
                "opacity" : "1"
            } , 400 , "linear" );
            $( newnotif ).remove();
            --Notification.INotifs;
        }
        if ( Notification.INotifs > 0 && Notification.INotifs < 3 && !Notification.TraversedAll ) {
            var lastnodeid = $( '#inotifs div.event:last-child' ).attr( "id" );
            var id = lastnodeid.substr( 6 );
            Coala.Warm( "notification/find" , {
                notifid : id,
                limit : "3"
            } );

        }
        //Notification.DecrementCount(); 

		return false;
	},
    DecrementCount: function () {
        var count = document.title.split( '(' )[ 1 ].split( ')' )[ 0 ];
        
        if ( count == '10+' ) {
            return;
        }
        --count;
        if ( count === 0 ) {
            document.title = 'Zino';
        }
        else {
            document.title = 'Zino (' + count + ')';
        }
    },
	AddFriend : function( eventid , theuserid ) {
		$( '#addfriend_' + theuserid  + ' a' )
		.fadeOut( 400 , function() {
			$( this )
			.parent()
			.empty()
			.append( document.createTextNode( 'Έγινε προσθήκη' ) );
		} );
		Coala.Warm( 'notification/addfriend' , { userid : theuserid } );
		Coala.Warm( 'notification/delete' , { eventid : eventid , relationnotif : false } );
        //Notification.DecrementCount();
		return false;
	},
	AddNotif : function( node ) {
		if ( Notification.VNotifs === 0 ) {
			Notification.VNotifs++;
			var notifscontainer = document.createElement( 'div' );
			var list = document.createElement( 'div' );
			var h3 = document.createElement( 'h3' );
			var expand = document.createElement( 'div' );
			var link = document.createElement( 'a' );
            var inotifsdiv = document.createElement( 'div' );

            $( inotifsdiv ).attr( "id" , "inotifs" ).addClass( "invisible" );
			$( expand ).addClass( "expand" ).append( link );
			$( h3 ).append( document.createTextNode( "Ενημερώσεις" ) );
			$( list ).addClass( "list" );
			$( notifscontainer ).addClass( "notifications" )
			.append( h3 ).append( list ).append( inotifsdiv ).append( expand );
			$( 'div.content div.frontpage' ).prepend( notifscontainer );
			//var notiflistheight = $( notiflist )[ 0 ].offsetHeight;
			$( link ).css( {
                "background-position" : "4px -1440px",
                "cursor" : "pointer"
            } ).attr( {
				title : "Απόκρυψη",
				href : ""
			} )
			.click( function() {
                if ( !Notification.Expanded ) {
                    $( this ).css( "background-position" , "4px -1440px" )
                    .attr( {
                        title : 'Απόκρυψη'
                    } );
                    Notification.Expanded = true;
                }
                else {  
                    $( this ).css( "background-position" , "4px -1252px" )
                    .attr( {
                        title : 'Εμφάνιση:'
                    } );
                    Notification.Expanded = false;
                }
                $( 'div.notifications div.list' ).slideToggle( "slow" );
			
                return false;
			} );
		}
		else if ( Notification.VNotifs < 5 ) {
			Notification.VNotifs++;
		}
		else {
			$( 'div.frontpage div.notifications div.list>div:last-child' ).animate( {
				opacity : "0",
				height: "0"
			} , 400 , "linear" , function() {
				var cloneit = $( this ).clone( true );
                $( "#inotifs" ).prepend( cloneit );
                $( this ).remove();
			} );
            Notification.INotifs++;
		}
		Notification.Show( node );
	},
	Show : function( node ) {
		$( 'div.notifications div.list' ).prepend( node );
		var targetheight = $( 'div.notifications div.list div.event' )[ 0 ].offsetHeight;
		$( node ).css( {
            'opacity' : '0',
            'height' : '0'
        } ).animate( {
			height: targetheight,
			opacity: "1"
		} , 400 , 'linear' )
		.mouseover( function() {
			$( this ).css( "border" , "1px dotted #666" ).css( "padding" , "4px" );
		} )
		.mouseout( function() {
			$( this ).css( "border" , "0" ).css( "padding" , "5px" );
		} );
	}
};
