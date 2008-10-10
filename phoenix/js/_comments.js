var Comments = {
	numchildren : {},
	Create : function( parentid ) {
		var texter;

		if ( parentid === 0 ) { // Clear new comment message
			texter = $( "div.newcomment div.text textarea" ).get( 0 ).value;
			$( "div.newcomment div.text textarea" ).get( 0 ).value = '';
		}
		else {
			texter = $( "#comment_reply_" + parentid + " div.text textarea" ).get( 0 ).value;
		}
		texter = $.trim( texter );
		if ( texter === "" ) {
			alert( "Δε μπορείς να δημοσιεύσεις κενό μήνυμα" );
			return;
		}

		var a = document.createElement( 'a' );
		a.onclick = function() {
				return false;
			};
		a.appendChild( document.createTextNode( "Απάντησε" ) );
		
		var indent = ( parentid===0 )?-1:parseInt( $( "#comment_" + parentid ).css( "paddingLeft" ), 10 ) / 20;
		
		var del = document.createElement( 'a' );
		del.onclick = function() {
            return false;
        };
		del.title = "Διαγραφή";
		del.style.marginRight = (parentid===0)?0:(indent+1)*20+'px';
		
		// Dimiourgisa ena teras :-S
		var daddy = ( parentid === 0 )? $( "div.newcomment:first" ).clone( true ):$( "#comment_reply_" + parentid );
		var temp = daddy.css( "opacity", 0 ).removeClass( "newcomment" ).find( "span.time" ).css( "marginRight", 0 ).text( "πριν λίγο" ).end()
		.find( "div.toolbox" ).append( del ).end()
		.find( "div.text" ).empty()./*html( texter.replace( /\n/gi, "<br />" ) )*/text( texter ).end()
		.find( "div.bottom" ).hide().empty().append( a ).append( document.createTextNode( " σε αυτό το σχόλιο" ) ).end();
		
		var valu = temp.find( "div.text" ).html();
		temp.find( "div.text" ).html( valu.replace( /\n/gi, "<br />" ) );
		
		//---------------------
		if ( parentid !== 0 ) {
			var kimeno = temp.find( "div.text" );
			var wid = ( $.browser.msie )?( kimeno.get( 0 ).offsetWidth-20 ):parseInt( kimeno.css( "width" ), 10 );
			kimeno.css( "width", wid-indent*20+'px' );
		}
		//----------------------

		var useros = temp.find( "div.who" ).get( 0 );
		useros.removeChild( useros.lastChild );
		useros.appendChild( document.createTextNode( " είπε:" ) );
		if ( parentid === 0 ) {
			temp.insertAfter( "div.newcomment:first" ).fadeTo( 400, 1 );
		}
		else {
			temp.insertAfter( "#comment_" + parentid ).fadeTo( 400, 1 );
			var deletes = $( "#comment_" + parentid + " div.toolbox a" ); // Hide parent's delete button
			if ( deletes.length > 0 && deletes.css( 'opacity' ) == 1 ) {
				deletes.fadeOut( 400 );
				deletes.parent().find( "span" ).css( "marginRight", indent*20 + 'px' );
			}
		}
		
		var type = temp.find( "#type:first" ).text();
		Comments.FixCommentsNumber( type, true );
		Coala.Warm( 'comments/new', { 	text : texter, 
										parent : parentid,
										compage : temp.find( "#item:first" ).text(),
										type : type,
										node : temp, 
										callback : Comments.NewCommentCallback
									} );
	},
	NewCommentCallback : function( node, id, parentid, newtext ) {
		if ( parentid !== 0 ) {
			++Comments.numchildren[ parentid ];
		}
		Comments.numchildren[ id ] = 0;
	
		var indent = ( parentid===0 )?-1:parseInt( $( "#comment_" + parentid ).css( "paddingLeft" ), 10 )/20;
		node.attr( 'id', 'comment_' + id );
		node.find( 'div.bottom' ).show().find( 'a' ).toggle( function() {
                Comments.Reply( id, indent+1 );
                return false;
            }, function() {
                $( '#comment_reply_' + id ).hide( 300, function() { $(this).remove(); } );
                return false;
            }
        );
		node.find( 'div.text' ).html( newtext ).get( 0 ).ondblclick = function() {
                Comments.Edit( id );
                return false;
            };
		node.find( 'div.toolbox a' ).get( 0 ).onclick = function() {
            Comments.Delete( id, parentid );
            return false;
        };
	},
	Reply : function( nodeid, indent ) {
		// Atm prefer marginLeft. When the comment is created it will be converted to paddingLeft. Looks better
		var temp = $( "div.newcomment:first" ).clone( true ).css( { marginLeft : (indent+1)*20 + 'px', opacity : 0 } ).attr( 'id', 'comment_reply_' + nodeid );
		temp.find( "div.toolbox span.time" ).css( { marginRight : (indent+1)*20 + 'px' } );
		temp.find( "div.bottom form input:first" ).get( 0 ).onclick = function() { // Only with DOM JS the onclick event is overwritten
					$( "#comment_reply_" + nodeid ).css( { marginLeft : 0, paddingLeft : (indent+1)*20 + 'px' } );
					Comments.Create( nodeid );
					return false;
				} ;

		temp.insertAfter( '#comment_' + nodeid ).fadeTo( 300, 1 );
		temp.find( "div.text textarea" ).get( 0 ).focus();
		//-----------------------------We do not know the width of the element until it is appended. Leave this piece of code here
		var wid = ( $.browser.msie )?( temp.find( "div.text textarea" ).get( 0 ).offsetWidth-20 ):parseInt( temp.find( "div.text textarea" ).css( "width" ), 10 );
		temp.find( "div.text textarea" ).css( "width", wid-(indent+1)*20+'px' );
		//-----------------------------
	},
	Edit : function( nodeid ) {
		var node = $( "#comment_" + nodeid );
		var text = node.find( "div.text" ).text();
		
		var textarea = document.createElement( 'textarea' );
		textarea.value = text;
		
		var div = document.createElement( 'div' );
		div.className = "bottom";
		
		var form = document.createElement( 'form' );
		form.onsubmit = function() {
					return false;
				};
				
		var input = document.createElement( 'input' );
		input.type = "submit";
		input.value = "Επεξεργασία";
		input.onclick = function() {
					var daddy = $( this ).parents().eq(2); // get big div
					var texter = daddy.find( "div.text textarea" ).get( 0 ).value;
					texter = $.trim( texter );
					if ( texter === '' ) {
						alert( "Δε μπορείς να δημοσιεύσεις κενό μήνυμα" );
						return;
					}
					daddy.find( "div.text" ).empty().append( document.createTextNode( texter ) ).end()
					.find( "div.bottom:last" ).remove().end()
					.find( "div.bottom" ).css( 'display', 'block' );
					Coala.Warm( 'comments/edit', {	id : daddy.attr( 'id' ).substring( 8 ),
													text : texter
												} );
				};
			
		var input2 = document.createElement( 'input' );
		input2.type = "reset";
		input2.value = "Ακύρωση";
		input2.onclick = function() {
					var daddy = $( this ).parents().eq(2); // get big div
					daddy.find( "div.text" ).empty().append( document.createTextNode( text ) ).end()
					.find( "div.bottom:last" ).remove().end()
					.find( "div.bottom" ).css( 'display', 'block' );
				};
		
		form.appendChild( input );
		form.appendChild( document.createTextNode( ' ' ) );
		form.appendChild( input2 );
		div.appendChild( form );
		
		node.find( "div.text" ).empty().append( textarea ).end()
		.find( "div.bottom" ).css( 'display', 'none' ).end()
		.append( div );
		node.find( "div.text textarea" ).get( 0 ).focus();
	}, 
	Delete : function( nodeid , parentid ) {
		var node = $( "#comment_" + nodeid );
		node.fadeOut( 450, function() { 
            $( this ).remove(); 
        } );
		Comments.FixCommentsNumber( node.find( "#type:first" ).text(), false );
        /*
        check whether the comment has parents
        */
        alert( 'parent id is ' + parentid );
        if ( parentid != 0 ) {
            var parent = $( '#' + parentid );
            var username;

            if ( $( 'a.profile span.imageview img' )[ 0 ] ) {
                username = $( 'a.profile span.imageview img' ).attr( 'alt' ); //get the username of the logged in user from the banner
            }
            else {
                //for users without avatar
                username = $( 'a.profile' ).text();
            }
            if ( username == $( parent ).find( 'div.who a img.avatar' ).attr( 'alt' ) ) {
                //if the comment is owned by the logged in user 
                //start checking if it has any children
                alert( 'parent belongs to loggedin user' );         
                var leftpadd = $( parent ).css( 'padding-left' );
                var value = leftpadd.substr( 0 , leftpadd.length - 2 ) - 0 + 20;
                var nextleftpadd = $( parent ).next().css( 'padding-left' );
                var nextvalue = nextleftpadd.substr( 0 , nextleftpadd.length - 2 ) - 0;
                if ( value == nextvalue ) {
                    alert( 'no other children' );
                    //the comment hasn't any children, show the deletion button 
                    var id = parent.id.substr( 8 , this.id.length - 8 );
                    $( parent ).find( 'span' ).css( 'margin-right' + value + 'px;' );
                    $( parent ).find( 'div.toolbox a' )
                    .removeClass( 'invisible' )
                    .click( function() {
                        if ( value == 0 ) {
                            return Comments.Delete( id , 0 ); 
                        }
                        else {
                            var prvid = $( parent ).prev( "div.comment[id=^'comment_']").id;
                            return Comments.Delete( id , prvid );
                        }
                    } );
                }
            }
        }
		Coala.Warm( 'comments/delete', { 
            commentid : nodeid
		} );
        return false;
	},
	DeleteCommentCallback : function( parentid ) {
        alert( 'parentid is ' + parentid );
        var parent = $( '#comment_' + parentid );
        var leftpadd = $( parent ).css( 'padding-left' );
        var value = leftpadd.substr( 0 , leftpadd.length - 2 ) - 0 + 20;
        var nextleftpadd = $( parent ).next().css( 'padding-left' );
        var nextvalue = nextleftpadd.substr( 0 , nextleftpadd.length - 2 ) - 0;
        alert( 'padding of parent comment is ' + value );
        alert( 'nextvalue padding is ' + nextvalue );
        if ( value != nextvalue ) {
            var id = parent.id.substr( 8 , this.id.length - 8 );
            $( this ).find( 'span' ).css( 'margin-right' + value + 'px;' );
            $( this ).find( 'div.toolbox a' )
            .removeClass( 'invisible' )
            .click( function() {
                return Comments.Delete( id ); 
            } );
        }
    },
	FixCommentsNumber : function( type, inc ) {
		if ( type != 2 && type != 4 ) { // If !Image or Journal
			return;
		}
		var node = $( "dl dd.commentsnum" );
		if ( node.length !== 0 ) {
			var commentsnum = parseInt( node.text(), 10 );
			commentsnum = (inc)?commentsnum+1:commentsnum-1;
			node.text( commentsnum + " σχόλια" );
		}
		else {
			var dd = document.createElement( 'dd' );
			dd.className = "commentsnum";
			dd.appendChild( document.createTextNode( "1 σχόλιο" ) );
			$( "div dl" ).prepend( dd );
		}
	},
    FindLeftPadding : function( node ) {
        var leftpadd = $( node ).css( 'padding-left' );
        if ( leftpadd ) {
            return leftpadd.substr( 0 , leftpadd.length - 2 ) - 0;
        }
        else {
            return false;
        }
    }
};
$( document ).ready( function() {
		$( "div.comments div.comment" ).not( ".newcomment" ).not( ".empty" ).each( function( i ) {
			var id = $( this ).attr( 'id' ).substring( 8 );
			var indent = parseInt( $( this ).css( 'paddingLeft' ), 10 )/20;
			//---------------------
			var kimeno = $( this ).find( "div.text" );
			var wid = ( $.browser.msie )?( kimeno.get( 0 ).offsetWidth-20 ):parseInt( kimeno.css( "width" ), 10 );
			kimeno.css( "width", wid-indent*20+'px' );
			//----------------------
			$( this ).find( "div.bottom a" ).unbind( "click" ).toggle( function() {
					Comments.Reply( id, indent );
					return false;
				}, function() {
					$( '#comment_reply_' + id ).hide( 300, function() { $(this).remove(); } );
					return false;
				}
			);
		} );
        //if the user is logged in
        if ( $( 'a.profile' )[ 0 ] ) {
            //if the page has at least one comment
            if ( $( "div.comment[id^='comment_']" )[ 0 ] ) { 
                var username;
                if ( $( 'a.profile span.imageview img' )[ 0 ] ) {
                    username = $( 'a.profile span.imageview img' ).attr( 'alt' ); //get the username of the logged in user from the banner
                }
                else {
                    //for users without avatar
                    username = $( 'a.profile' ).text();
                }
                $( "div.comments div.comment[id^='comment_']" ).each( function() {    
                    if ( username == $( this ).find( 'div.who a img.avatar' ).attr( 'alt' ) ) {
                        var leftpadd = Comments.FindLeftPadding( this ) + 20;
                        var nextleftpadd = Comments.FindLeftPadding( $( this ).next()[ 0 ] );
                        if ( leftpadd != nextleftpadd ) {
                            var id = this.id.substr( 8 , this.id.length - 8 );
                            $( this ).find( 'span' ).css( 'margin-right' + leftpadd + 'px;' );
                            //find parent of the comment
                            if ( leftpadd !== 0 ) {
                                var node = $( this )[ 0 ];
                                do { 
                                    node = $( node ).prev()[ 0 ];
                                    if ( node ) {
                                        lefter = Comments.FindLeftPadding( node );
                                        //alert( 'left padding is ' + lefter );
                                    }   

                                } while( node && lefter + 20 != leftpadd )
                                /*
                                while ( node && lefter - 20 != leftpadd  && lefter >= 0  ) {
                                    node = $( node ).prev( "div.comment[id^='comment_']")[ 0 ];
                                    if ( node ) {
                                        lefter = Comments.FindLeftPadding( node );
                                    }
                                }
                                */
                                //$( node ).css( 'border' , '1px solid red' );
                            }
                            $( this ).find( 'div.toolbox a' )
                            .removeClass( 'invisible' )
                            .click( function() {
                                if ( leftpadd === 0 ) {
                                    return Comments.Delete( id , 0 ); 
                                }
                                else {
                                    $( '#' + node.id ).css( 'border' , '1px solid red' );
                                    return Comments.Delete( id , node.id );
                                }
                            } );
                        }
                    }
                } );

                
            }
        }
} );
