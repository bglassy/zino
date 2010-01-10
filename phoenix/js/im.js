var IM = {
    OnMessageArrival: function ( shoutid, text, who, channel ) {
        document.title = 'Message received from ' + who.name + ': ' + text;
    }
};

if ( typeof Frontpage == 'undefined' ) {
    var Frontpage = {
        Shoutbox: {
            OnMessageArrival: IM.OnMessageArrival
        }
    };
}
else {
    Frontpage.Shoutbox.OnMessageArrival = ( function ( old ) {
        return function ( shoutid, text, who, channel ) {
            alert( who.name );
            old( shoutid, text, who, channel );
            IM.OnMessageArrival( shoutid, text, who, channel );
        };
    } )( Frontpage.Shoutbox.OnMessageArrival );
}

