<?php
    include 'rabbit/xml.php';
    
    global $xhtmlsanitizer_goodtags;
    global $xhtmlsanitizer_noautoclose;
    
    $xhtmlsanitizer_noautoclose = array( // these tags cannot be <autoclosed /> but have to be <closed></closed> explicitly
        'td' => true,
        'div' => true
    );
    
    $xhtmlsanitizer_goodtags = XHTMLSanitizer_DecodeTags( array(
        'a' => array( 'coords', 'href', 'hreflang', 'name', 'rel', 'rev', 'shape', 'target', 'type' ), 
        'abbr', 'acronym', 'address',
        'area' => array( 'coords', 'href', 'nohref', 'shape', 'target' ), 
        'b', 'bdo', 'big', 
        'blockquote' => array( 'cite' ), 
        'br', 
        'button' => array( 'disabled', 'type', 'value' ), 
        'caption', 'cite', 'code', 
        'col' => array( 'span' ),
        'colgroup' => array( 'span' ),
        'dd', 'del', 'div', 'dfn', 'dl', 'dt', 'em', 'fieldset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'hr', 'i',
        'img' => array( 'src', 'alt', 'border', 'height', 'ismap', 'longdesc', 'usemap', 'vspace', 'width' ), 
        'ins' => array( 'cite', 'datetime' ),
        'kdb', 'label', 'legend',
        'li' => array( 'type', 'value' ),
        'map' => array( 'map' ),
        'noframes', 'noscript', 'ol', 'optgroup', 'option', 'p', 
        'q' => array( 'cite' ),
        'samp', 'small', 'strong', 'sub', 'sup', 
        'span' => array( 'class' ),
        'table' => array( 'cellpadding', 'cellspacing', 'rules', 'summary' ),
        // 'tbody', // preserved but not outputted
        'td' => array( 'abbr', 'colspan', 'rowspan' ),
        'textarea' => array( 'cols', 'rows' ), 'tfoot', 
        'th' => array( 'scope', 'colspan', 'colspan' ),
        'thead', 'tr', 'tt', 
        'ul' => array( 'compact', 'type' ),
        // and only for safe sources...
        'object' => array( 'width', 'height' ),
        'param' => array( 'name', 'value' ),
        'embed' => array( 'src', 'type', 'width', 'height' ),
        '' => array( 'title', 'lang', 'dir', 'accesskey', 'tabindex', 'class' ) // everywhere
    ) );
    
    function XHTMLSanitizer_DecodeTags( $tags ) {
        $ret = array();
        foreach ( $tags as $key => $value ) {
            if ( is_string( $value ) ) {
                $ret[ $value ] = true;
            }
            else if ( is_array( $value ) ) {
                $ret[ $key ] = array();
                foreach ( $value as $attribute ) {
                    $ret[ $key ][ $attribute ] = true;
                }
            }
        }
        return $ret;
    }
    
    class XHTMLSanitizer {
        private $mSource;
        private $mAllowedTags;
        private $mTextProcessor;
        private $mMaxLength = false;
        private $mMaxTrimString = '...';
        private $mCurrentLength = 0;
        
        public function XHTMLSanitizer() {
            $this->mAllowedTags = array();
            $this->mSource = false;
            $this->mTextProcessor = false;
        }
        public function SetTextProcessor( $textprocessor ) {
            $this->mTextProcessor = $textprocessor;
        }
        public function SetMaxLength( $length = false ) {
            w_assert( $length === false || is_int( $length ) );
            $this->mMaxLength = $length;
        }
        public function SetSource( $source ) {
            global $water;
            
            if ( $source === true ) {
                $water->Notice( 'XHTMLSanitizer source was a boolean; converting to string' );
                $source = '1';
            }
            else if ( $source === false ) {
                $water->Notice( 'XHTMLSanitizer source was a boolean; converting to string' );
                $source = '';
            }
            else if ( is_array( $source ) ) {
                $water->Warning( 'XHTMLSanitizer source was an array; skipping' );
                $source = '';
            }
            else if ( is_object( $source ) ) {
                $water->Warning( 'XHTMLSanitizer source was an object; skipping' );
                $source = '';
            }
            $source = ( string )$source;
            $this->mSource = $source;
        }
        private function RemoveComments( $htmlsource ) {
            global $water;
            
            if ( preg_match( '#\<\!--(.*?)\<\!--(.*?)--\>#', $htmlsource ) ) {
                $water->Warning( 'XHTMLSanitizer: Call me paranoid, but finding \'<!--\' inside this comment makes me suspicious' );
            }
            return preg_replace( '#\<\!--(.*?)--\>#', '', $htmlsource );
        }
        private function ReduceWhitespace( $htmlsource ) {
            return preg_replace( "#([ \t\n\r]+)#", ' ', $htmlsource );
        }
        public function GetXHTML() {
            global $water;
            global $rabbit_settings;
            
            w_assert( $this->mSource !== false, 'Please SetSource() before calling GetXHTML()' );
            
            $tags = array();
            
            $source = $this->mSource;
            
            $source = $this->RemoveComments( $source );
            
            $descriptors = array(
                0 => array( "pipe", "r" ),
                1 => array( "pipe", "w" ),
                2 => array( "pipe", "r" )
            );
            
            $process = proc_open( '/home/dionyziz/sanitizer/sanitize', $descriptors, $pipes, '.', array() );
            
            if ( !is_resource( $process ) ) {
                throw New Exception( 'Failed to start the XHTML sanitizer' );
            }
            
            fwrite( $pipes[ 0 ], $source );
            fclose( $pipes[ 0 ] );
            
            $ret = stream_get_contents( $pipes[ 1 ] );
            
            ob_start();
            var_dump( $ret );
            $tidied = ob_get_clean();
            
            $water->Trace( 'Sanitizer tidied up document', $tidied );
            
            fclose( $pipes[ 1 ] );
            
            $returnvalue = proc_close( $process );
            
            $water->Trace( 'Sanitizer exited with status ' . $returnvalue );
            
            $ret = trim( $this->ReduceWhitespace( $ret ) );
            $ret = str_replace( '&nbsp;', ' ', $ret );
            $parser = New XMLParser( '<body>' .$ret . '</body>' );
            $parser->ignoreEmptyTextNodes( false );
            $body = $parser->Parse();
            
            if ( $body === false ) {
                return '';
            }
            w_assert( $body->nodeName == 'body' );
            
            $ret = trim( $this->XMLInnerHTML( $body ) );
            
            return $ret;
        }
        public function SanitizeURL( $url ) {
            static $validprotocols = array(
                'http', 'ftp', 'https', 'mailto', 'irc'
            );
            
            if ( strpos( $url, ':' ) !== false ) {
                $safe = false;
                foreach ( $validprotocols as $protocol ) {
                    if ( substr( $url, 0, strlen( $protocol ) ) == $protocol ) {
                        $safe = true;
                        break;
                    }
                }
                if ( !$safe ) {
                    return false;
                }
            }
            
            return $url;
        }
        public function SanitizeTrustedURL( $url ) {
            $url = $this->SanitizeURL( $url );

            if ( $url === false ) {
                return false;
            }
            if ( !preg_match( '#^http\://www\.youtube\.com/#', $url ) ) {
                return false;
            }
            return $url;
        }
        private function XMLOuterHTML( XMLNode $root ) {
            global $xhtmlsanitizer_noautoclose;
            
            if ( !isset( $this->mAllowedTags[ $root->nodeName ] ) ) {
                return $this->XMLInnerHTML( $root );
            }
            if ( $this->mMaxLength !== false && $this->mCurrentLength >= $this->mMaxLength ) {
                return '';
            }
            
            $tagrule = $this->mAllowedTags[ $root->nodeName ];
            
            $ret = '<' . $root->nodeName;
            
            $attributes = array();
            foreach ( $root->attributes as $attribute => $value ) {
                if ( $tagrule->AttributeAllowed( $attribute ) ) {
                    if ( !empty( $value ) || ( $root->nodeName == 'img' && $attribute == 'alt' ) ) {
                        if ( $attribute == 'href' || $attribute == 'src' || $attribute == 'longdesc' ) {
                            $value = $this->SanitizeURL( $value );
                            if ( empty( $value ) ) {
                                continue;
                            }
                        }
                        if ( $root->nodeName == 'embed' ) {
                            if ( $attribute == 'src' ) {
                                $value = $this->SanitizeTrustedURL( $value );
                                if ( empty( $value ) ) {
                                    continue;
                                }
                            }
                        }
                        $attributes[] = $attribute . '="' . htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ) . '"';
                    }
                }
            }
            
            if ( !empty( $attributes ) ) {
                $ret .= ' ' . implode( ' ', $attributes );
            }
            
            if ( empty( $root->childNodes ) ) {
                if ( isset( $xhtmlsanitizer_noautoclose[ $root->nodeName ] ) ) {
                    $ret .= '></' . $root->nodeName . '>';
                }
                else {
                    $ret .= '/>';
                }
            }
            else {
                $ret .= '>';
                $ret .= $this->XMLInnerHTML( $root );
                $ret .= '</' . $root->nodeName . '>';
            }
            
            return $ret;
        }
        private function XMLInnerHTML( XMLNode $root ) {
            if ( $root->nodeName == 'script' ) { // boo
                return '';
            }
            
            $ret = '';
            foreach ( $root->childNodes as $xmlnode ) {
                if ( is_string( $xmlnode ) ) {
                    $atmax = false;
                    if ( $this->mMaxLength !== false && $this->mCurrentLength + mb_strlen( $xmlnode ) > $this->mMaxLength ) {
                        $xmlnode = mb_substr( $xmlnode, 0, $this->mMaxLength - $this->mCurrentLength + 1 );
                        $xmlnode .= $this->mMaxTrimString;
                        $atmax = true;
                    }
                    $this->mCurrentLength += strlen( $xmlnode );
                    if ( $this->mTextProcessor !== false ) {
                        $callback = $this->mTextProcessor;
                        $ret .= $callback( $xmlnode );
                    }
                    else {
                        $ret .= htmlspecialchars( $xmlnode, ENT_COMPAT, 'UTF-8' );
                    }
                    if ( $atmax ) {
                        return $ret;
                    }
                }
                else {
                    $ret .= $this->XMLOuterHTML( $xmlnode );
                }
            }
            return $ret;
        }
        public function AllowTag( XHTMLSaneTag $tag ) {
            global $water;
            global $xhtmlsanitizer_goodtags;
            
            if ( !isset( $xhtmlsanitizer_goodtags[ $tag->Name() ] ) ) {
                $water->Notice( 'XHTMLSanitizer tag "' . $tag->Name() . '" is not safe or valid' );
                return;
            }

            $this->mAllowedTags[ $tag->Name() ] = $tag;
        }
    }
    
    class XHTMLSaneTag {
        private $mName;
        private $mAllowedAttributes;
        
        public function Name() {
            return $this->mName;
        }
        public function AttributeAllowed( $attributename ) {
            w_assert( is_string( $attributename ) );
            return isset( $this->mAllowedAttributes[ $attributename ] );
        }
        public function XHTMLSaneTag( $tagname ) {
            w_assert( is_string( $tagname ) );
            w_assert( preg_match( '#^[a-z0-9]+$#', $tagname ) );
            $this->mName = $tagname;
            
            if ( $tagname == 'img' ) {
                $this->AllowAttribute( New XHTMLSaneAttribute( 'alt' ) );
            }
        }
        public function AllowAttribute( XHTMLSaneAttribute $attribute ) {
            global $water;
            global $xhtmlsanitizer_goodtags;
            
            if ( !isset( $xhtmlsanitizer_goodtags[ $this->mName ][ $attribute->Name() ] ) && !isset( $xhtmlsanitizer_goodtags[ '' ][ $attribute->Name() ] ) ) {
                $water->Notice( 'XHTMLSanitizer attribute "' . $attribute->Name() . '" is not safe or valid for tag "' . $this->mName . '"' );
                return;
            }
            
            $this->mAllowedAttributes[ $attribute->Name() ] = $attribute;
        }
    }
    
    class XHTMLSaneAttribute {
        private $mName;
        
        public function Name() {
            return $this->mName;
        }
        public function XHTMLSaneAttribute( $attributename ) {
            w_assert( is_string( $attributename ) );
            w_assert( preg_match( '#^[a-z]+$#', $attributename ) );
            $this->mName = $attributename;
        }
    }
?>
