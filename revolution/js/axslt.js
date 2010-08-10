/**
 * axslt.js - Version 1.0 ( 12:19 UTC 4/21/2010 )
 *
 * Copyright (c) 2010 Tzortzidis Alexandros ( chorvus@gmail.com )
 * Project page: <http://chorvus.com/axslt>
 * 
 * Changelog:
 *      1.0 ( 12:19 UTC 4/21/2010 ) - First cross-browser working release
 *      0.9 ( 22:18 UTC 4/19/2010 ) - Initial release             
 * 
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
 * NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
 
 /*
 * ----Compact parameter mode.
 * Example call: xhr.transform( { 'name': templateName,
 *                                'mode': templateMode }, callback, xslPath );
 * ----Normal parameter mode.
 * Example call: xhr.transform( templateName, callback, templateMode, params, xslPath );
 */

var axslt = function( xml, template, callback, params, xslPath ) {
    var templateMode;
    var templateName;
    if ( template.substr( 0, 5 ) == 'call:' ) {
        templateName = template.substr( 5 );
        templateMode = 'call';
    }
    else {
        if ( template instanceof Object ) {
            templateName = template['name'];
            if ( template[ 'type' ] == 'call' ) {
                templateMode = 'call';
            }
            else if ( template[ 'type' ] == 'apply' || !_aXSLT.defaultMode ) {
                templateMode = 'apply';
            }
            else {
                templateMode = _aXSLT.defaultMode;
            }
        }
        else {
            templateName = template;
            if ( templateMode != 'call' ) {
                templateMode = 'apply';
            }
        }
    }
    if ( !xslPath ) {
        if ( !_aXSLT.defaultStylesheet ) {
            //console.error( 'aXSLT: Please specify a (default) stylesheet' );
            return;
        }
        xslPath = _aXSLT.defaultStylesheet;
    }
    _aXSLT.registerUnit( xml, xslPath, callback, templateName, templateMode, params );
}

var node_dump = function( nodeset ) {
    var xmls =  new XMLSerializer();
    var alertstring = '//Total nodes: ' + nodeset.length + '\n';
    for ( var i = 0; i < nodeset.length; i++ ) {
         alertstring += '//Node [' + i + ']:\n' + xmls.serializeToString( nodeset[ i ] ) + '\n\n';
    }
    return alertstring;
}

var node_strip = function( nodeset ) {
    var ret = [];
    for ( var i = 0; i < nodeset.length; ++i ) {
        if ( i == 0 ) {
            if ( nodeset[ 0 ].nodeType = Node.TEXT_NODE ) {
                var nodetext = nodeset[ 0 ].nodeValue.replace( /(\s)+/g, '' );
                if ( nodetext != '' ) {
                    //nodeset[ 0 ].nodeValue = nodetext;
                    ret.push( nodeset[ 0 ] );
                }
            }
        }
        else if ( nodeset.length > 2 && i == nodeset.length - 1 ) {
            var nodetext = nodeset[ i ].nodeValue.replace( /(\s)+/g, '' );
            if ( nodetext != ''  ) {
                if ( nodetext != '' ) {
                    //nodeset[ 0 ].nodeValue = nodetext;
                    ret.push( nodeset[ 0 ] );
                }
            }
        }
        else {
            ret.push( nodeset[ i ] );
        }
    }
    return ret;
}

var _aXSLT = {
    defaultStylesheet: false,
    defaultMode: false,
    pendingUnits: {},
    lastUnitIndex: 1,
    unitLists: {},
    lastListIndex: 1,
    xslCache: {},
    prepareXML: function( xml ) {
        //TODO check
        var index = this.lastListIndex++;
        this.unitLists[ index ] = [];
        if ( !this.xmlReady( xml ) ) {
            xml.onreadystatechange = ( function( xml, i ) {
                return function() {
                    _aXSLT.checkXML( xml, i );
                };
            } )( xml, index ); //code magic
        }
        return index;
    },
    prepareXSL: function( path ) {
        if ( this.xslCache[ path ] ) { //If the xsl is already cached, escape the procedure
            return this.xslCache[ path ].index;
        }
        
        var index = this.lastListIndex++;
        var xhr;
        if ( window.ActiveXObject ) {
            //xhr = new ActiveXObject( 'MSXML2.FreeThreadedDOMDocument' );
            try {
                xhr = new ActiveXObject( 'Msxml2.XMLHTTP.6.0' );
            }
            catch ( err ) {
                try {
                    xhr = new ActiveXObject( 'Msxml2.XMLHTTP.3.0' );
                }
                catch ( err ) {
                    try {
                        xhr = new ActiveXObject( 'Msxml2.XMLHTTP' );
                    }
                    catch ( err ) {
                        return false;
                    }
                }
            }
        }
        else if ( window.XMLHttpRequest ) {
            xhr = new XMLHttpRequest();
        }
        xhr.onreadystatechange = ( function( xhr, i ) {
            return function() {
                _aXSLT.checkXSL( xhr, i );
            };
        } )( xhr, index ); //code magic
        xhr.open( 'GET', path, true );
        xhr.send( null );
        this.unitLists[ index ] = [];
        this.xslCache[ path ] = { 'xhr': xhr, 'index': index };
        return index;
    },
    registerUnit: function( xml, xslpath, callback, templateName, templateMode, params ) {
        var xslindex = this.prepareXSL( xslpath );
        if ( this.xmlReady( xml ) && this.xslCache[ xslpath ].xhr.readyState == 4 ) {
            this.transform( xml, this.xslCache[ xslpath ].xhr, callback, templateName, templateMode, params );
            return;
        }
        var xmlindex = this.prepareXML( xml );
        this.enQueue( xml, xmlindex, this.xslCache[ xslpath ].xhr, xslindex, callback, templateName, templateMode, params );
    },
    enQueue: function( xml, xmlindex, xsl, xslindex, callback, templateName, templateMode, params ) {
        var unit = {
            'xml': xml,
            'xmlindex': xmlindex,
            'xslindex': xslindex,
            'xsl': xsl,
            'name': templateName,
            'mode': templateMode,
            'params': params,
            'callback': callback
        };
        var index = _aXSLT.lastUnitIndex++;
        this.unitLists[ xslindex ].push( index );
        this.unitLists[ xmlindex ].push( index );
        this.pendingUnits[ index ] = unit;
    },
    _indexOf: function( needle, haystack ) {
        if ( haystack.length ) {
            for ( var i = 0; i <= haystack.length; ++i ) {
            //alert( 'haystack: '+haystack[i]+' needle:' + needle );
                if ( haystack[ i ] == needle ) {
                //alert( 'return!' );
                    return i;
                }
            }
        }
        return -1;
    },
    deQueue: function( index ) {
        //alert( 'dequeu' );
        var unit = _aXSLT.pendingUnits[ index ];
        //alert( this._indexOf( index, this.unitLists[ unit.xmlindex ] ) );
        //alert( 'before' + this.unitLists[ unit.xslindex ].length );
        this.unitLists[ unit.xslindex ].splice( this._indexOf( index, this.unitLists[ unit.xslindex ] ) , 1, false );
        //alert( unit.xslindex );
        //alert( 'after' + this.unitLists[ unit.xslindex ].length );
        //alert( 'before' + this.unitLists[ unit.xmlindex ].length );
        this.unitLists[ unit.xmlindex ].splice( this._indexOf( index, this.unitLists[ unit.xmlindex ] ) , 1, false );
        //alert( 'after' + this.unitLists[ unit.xmlindex ].length );
        delete _aXSLT.pendingUnits[ index ];
        //alert( typeof( _aXSLT.pendingUnits[ index ] ) );
    },
    checkXML: function( xml, index ) {
        if ( !this.xmlReady( xml ) ) {
            return;
        }
        var pending = _aXSLT.unitLists[ index ].slice(); //cloning the array, because the dequeue of successfully transformed units break the iteration behaviour
        for ( var i = 0; i < pending.length; ++i ) {
            if ( _aXSLT.pendingUnits[ pending[ i ] ].xsl.readyState == 4 ) {
                _aXSLT.transformUnit( pending[ i ] );
            }
        }
    },
    checkXSL: function( xsl, index ) {
        if ( xsl.readyState != 4 ) {
            return;
        }
        //alert( index );
        //alert( _aXSLT.unitLists[ index ] );
        var pending = _aXSLT.unitLists[ index ].slice(); //cloning the array, because the dequeue of successfully transformed units break the iteration behaviour
        for ( var i = 0; i < pending.length; i++ ) {
            if ( this.xmlReady( _aXSLT.pendingUnits[ pending[ i ] ].xml ) ) { //test
                _aXSLT.transformUnit( pending[ i ] );
            }
        }
    },
    transformUnit: function( unitIndex ) {
        var unit = this.pendingUnits[ unitIndex ];
        this.transform( unit.xml, unit.xsl, unit.callback, unit.name, unit.mode, unit.params );
        //alert( 'transform unit' );
        this.deQueue( unitIndex );
    },
    expandParams: function( params ) {
        var ret = '';
        var par;
        for ( par in params ) {
            if ( typeof( par ) == 'string' ) {
                ret += '<xsl:with-param name="' + par + '">' + params[ par ] + '</xsl:with-param>';
            }
        }
        return ret;
    },
    addTemplate: function( basicStylesheet, templateName, templateMode, params ) {
        if ( !templateName || templateName == '/' ) {
            return basicStylesheet;
        }
        var templateString =
            '<xsl:template match="/' + ( window.ActiveXObject ? '*' : '' ) + '" priority="500000">' +
                ( templateMode == 'call' ?
                    '<xsl:call-template name="' + templateName + '">' +
                        _aXSLT.expandParams( params ) +
                    '</xsl:call-template>'
                :
                    '<xsl:apply-templates select="' + templateName + '" />'
                ) +
            '</xsl:template>';
        var templateDOM;
        //alert( templateString );
        if ( window.DOMParser ) {
            templateString =
                '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">' +
                    templateString +
                '</xsl:stylesheet>';
            templateDOM = new DOMParser().parseFromString( templateString, 'text/xml' ).childNodes[0].childNodes[0];
            if ( basicStylesheet.childNodes[0].nodeName == 'html' ) {
                throw new Error( 'aXSLT: The xsl file has an html structure' );
            }
            basicStylesheet.childNodes[0].appendChild( basicStylesheet.importNode( templateDOM, true ) );
        }
        else {
            var offset = 0;
            var styleEndTagStart = basicStylesheet.indexOf( '</xsl:stylesheet>' );
            //alert( basicStylesheet.substring( styleEndTagStart - 50, 50 ) );
            var xmlBefore = basicStylesheet.substring( 0, styleEndTagStart );
            //alert( xmlBefore.substring(  xmlBefore.length - 150 ) );
            /*
            var styleSheetEnd = basicStylesheet.indexOf( '>', styleSheetStart ) + 1;
            var xmlBefore = basicStylesheet.substring( 0, styleSheetEnd );
            var xmlAfter = basicStylesheet.substring( styleSheetEnd );
            
            //alert( xmlAfter ); */
            basicStylesheet = xmlBefore + templateString + '</xsl:stylesheet>';
            //alert( basicStylesheet.substring(  basicStylesheet.length - 350 ) );
        }
        /*
        else if ( window.ActiveXObject ) {
            var finalDoc = new ActiveXObject('MSXML2.FreeThreadedDOMDocument');
            var doc = new ActiveXObject('MSXML2.FreeThreadedDOMDocument');
            return;
            //var doc = new ActiveXObject('Microsoft.XMLDOM');
	        doc.async = 'false';
	        doc.loadXML( templateString );
			//alert( basicStylesheet.childNodes[0].childNodes.length );
            finalDoc.appendChild( basicStylesheet.childNodes[0] );
            alert( finalDoc.childNodes.length );
            finalDoc.childNodes[0].appendChild( doc.childNodes[0].childNodes[0] );
			//alert( basicStylesheet.childNodes[0].childNodes.length );
            //alert( templateDOM.nodeName );
            //alert( 'exortum ' + templateDOM );
            //alert( templateDOM.document );
            //alert( templateDOM.nodeName );
            //basicStylesheet.childNodes[0].appendChild( 
            //basicStylesheet.childNodes[0].appendChild( templateDOM );
        } */
        return basicStylesheet;
    },
    xmlReady: function( xml ) {
        if ( !xml || typeof( xml ) == 'string' ) {
            return true;
        }
        return ( xml.readyState == 4 );
    },
    transform: function( xml, xsl, callback, templateName, templateMode, params ) {
        if ( !this.xmlReady( xml ) || xsl.readyState != 4 ) {
            return false;
        }
        
        var xmldoc;
        var result;
        var processor;
        var stylesheet;
        
        if ( window.ActiveXObject ) {
            stylesheet = xsl.responseText;
        }
        //else if ( document.DOMParser && !xsl.responseXML && xsl.responseText ) { //TODO: remove true
        else if ( window.DOMParser && !xsl.responseXML && xsl.responseText ) { //TODO: remove true
            //Gecko workaround
            stylesheet = new DOMParser().parseFromString( xsl.responseText, 'text/xml');
        }
        else {
            stylesheet = xsl.responseXML;
        }
        stylesheet = _aXSLT.addTemplate( stylesheet, templateName, templateMode, params );
        //alert( stylesheet );
        if ( !stylesheet ) {
            throw new Error( 'aXSLT: Error in template juggling' );
            return;
        }
        
        if ( typeof( xml ) == 'string' ) {
            if ( window.DOMParser ) {
                new DOMParser().parseFromString( xsl, 'text/xml' );
            }
            else if ( window.ActiveXObject ) {
                xmldoc = new ActiveXObject("Microsoft.XMLDOM");
                xmldoc.async = "false";
                xmldoc.loadXML( xml );
            }
        }
        else if ( !xml ) {
            //xmldoc = document.implementation.createDocument( null, null, null);
            if ( window.DOMParser ) {
                xmldoc = new DOMParser().parseFromString( '', 'text/xml' );
                //alert( xmldoc.documentElement.nodeName );
            }
            else if ( window.ActiveXObject ) {
                xmldoc = '<html />';
            }
        }
        else {
            if ( window.ActiveXObject ) {
                xmldoc = xml.responseText;
            }
            else if ( xml.responseXML ) {
                xmldoc = xml.responseXML;
            }
        }
        
        if ( window.ActiveXObject ) {
            
            //alert( xmldoc.nodeName );
            //alert( xml.responseText );
            //var xsldoc = new ActiveXObject("Msxml2.FreeThreadedDOMDocument.3.0");
            var xmldom;
            if ( typeof( xmldoc ) == 'string' ) {
                xmldom = new ActiveXObject("Microsoft.XMLDOM");
                xmldom.async = 'false';
                xmldom.loadXML( xmldoc );
            }
            else {
                xmldom = xmldoc;
            }
            
            //alert( xmldom.xml );
            var	xsldom = new ActiveXObject("Microsoft.XMLDOM");
            xsldom.async = 'false';
            //$( '#world' ).empty().text( stylesheet + xmldoc );
            xsldom.loadXML( stylesheet );
            
            var div = document.createElement( 'body' );
            
            //var container = new ActiveXObject("Microsoft.XMLDOM");
            //var container = new ActiveXObject("htmlfile");
            //container.async = 'false';
            
            //$( '#world' ).empty().text( xmldom.documentElement.transformNode( xsldom.documentElement ) );
            var transxml = xmldom.documentElement.transformNode( xsldom.documentElement );
            var startDTD = transxml.indexOf( '<!DOCTYPE' );
            //Strip DTD to avoid text chunking - IE hate points: ***
            if ( startDTD >= 0 ) {
                var endDTD = transxml.indexOf( '>', startDTD ) + 1;
                transxml = transxml.substring( endDTD );
            }
            
            //Special <option> tags case - IE hate points: ****
            if ( /(\s*<option(\s+[a-z:-]+=("[^"]*"|\'[^\']*\'))*>.*<\/option>\s*)+/i.test( transxml ) ) {
                transxml = '<select>' + transxml + '</select>';
                div.innerHTML = transxml;
                _aXSLT.postTransform( div.childNodes[0], callback );
            }
            else {
                div.innerHTML = transxml;
                _aXSLT.postTransform( div, callback );
            }
            //container.loadXML( transxml );
            //return;
            //alert( '>' + div.innerHTML + '<' );
            //alert( '>' + container.xml + '<' );
            //alert( div.innerHTML )
            
            /*
            var xsltdom = document.createElement( 'xml' );
            xsltdom.change = function() {
                if ( xsltdom.readyState == 'complete
            }
            xsltdom.async = 'false';
            //alert( xmldom.documentElement.transformNode( xsldom.documentElement ) );
            xsltdom.loadXML( xmldom.documentElement.transformNode( xsldom.documentElement ) );
            //alert( xsltdom.readyState );*/
            
            
            //xsldoc.async = false;
            //xsldoc.innerXML = stylesheet;
            //for ( i in xsldoc ) {
            //alert( i );
            //}
            //alert( xsldoc.childNodes.length );
            /*
            var XSLTc = new ActiveXObject("MSXML2.XSLTemplate");
            XSLTc.stylesheet = xsl.responseXML;
            var XSLTProc = XSLTc.createProcessor();
            XSLTProc.input = xmldoc;
            XSLTProc.transform();
            var xmlstring = XSLTProc.output;            
            result = document.createElement( 'div' );
            result.innerHTML = xmlstring;
            //alert( stylesheet );*/
            
            
            /*var xmldoc = document.createElement( 'xml' );
            var xsldoc = document.createElement( 'xml' );
            var div = document.createElement( 'div' );
            var transformed = false;
            var change = function() {
                //alert( xmldoc.readyState + ' ' + xsldoc.readyState );
                if ( xmldoc.readyState == 'complete' && xsldoc.readyState == 'complete' && !transformed ) {
                    //alert( xmldoc.XMLDocument.childNodes[0].nodeName );
                    //alert( xmldoc.XMLDocument.childNodes[1].nodeName );
                    //alert( xmldoc.XMLDocument.childNodes[2].nodeName );
                    setTimeout( function() {
                    result = xmldoc.transformNode( xsldoc.XMLDocument );
                    alert( result.childNodes.length );
                    transformed = true;
                    _aXSLT.postTransform( result, callback );
                    }, 1000 );
                }
            }
            xmldoc.onreadystatechange = change;
            xsldoc.onreadystatechange = change;
            if ( typeof( xmldoc.innerHTML ) != 'undefined' ) {
                xmldoc.innerHTML = xml.responseText;
                xsldoc.innerHTML = stylesheet;
            }
            else {
                xmldoc.src = xml.responseText;
                xsldoc.src = stylesheet;
            }
            div.appendChild( xmldoc );
            div.appendChild( xsldoc );
            //alert( 'here' );
            //alert( xmldoc.childNodes.length );
            //var result = $( 'div' ).append( xmldoc ).append( xsldoc )[0];
            //alert( div.childNodes.length );
            //alert( $( div.childNodes ).filter( '*' ).length ); */
        }
        else if ( window.XSLTProcessor ) {
            processor = new XSLTProcessor();
            processor.importStylesheet( stylesheet );
            //console.warn( xmldoc );
            //console.warn( stylesheet );
            //console.warn( new XMLSerializer().serializeToString( stylesheet ) );
            result = processor.transformToFragment( xmldoc, document);
            //alert( new XMLSerializer().serializeToString( result ) );
            _aXSLT.postTransform( result, callback );
        }
    },
    postTransform: function( result, callback ) {
        if ( !result ) {
            throw new Error( 'aXSLT: Empty result document' );
            return null;
        }
        if ( callback ) {
            callback.call( result.childNodes );
        }
    }
}
