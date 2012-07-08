<?php
/**********
 * riseTemplate 
 *   template engine of PHP
 *   version 0.2
 **/
class riseTemplateLibrary{
    protected $set_vals = array();
    public $core = null;
    public function setCore( $core ){
        $this->core = $core;
    }
    public function set( $args ){
        $len = sizeof( $args );
        if( $len < 2 ){
            return "";
        }
        $n = 0;
        while( isset( $args[$n+1] ) ){
            $this->set_vals[ $args[$n] ] = $args[$n+1];
            $n+=2;
        }
        return "";
    }
    public function replaceSets( $contents ){
        foreach( $this->set_vals as $k=>$v ){
            $contents = str_replace( '*'.$k.'*' , $v , $contents );
        }
        return $contents;
    }
    public function tag( $tag , $args , $double = true , $has_br = false ){
        if( !isset( $args[0] ) ){
            $args[0] = "";
        }
        if( !isset( $args[1] ) ){
            $args[1] = "";
        }
        if( $args[0] == 'null' ){
            $args[0] = "";
        }
        if( $args[1] == 'null' ){
            $args[1] = "";
        }

        if( $args[0] !== "" ){
            $args[0] = " ".$args[0]." ";
        }

        if( $double ){
            if( $has_br ){
                return sprintf( "<%s%s>\r\n%s\r\n</%s>" , $tag , $args[0] , $args[1] , $tag);
            }else{
                return sprintf( "<%s%s>%s</%s>" , $tag , $args[0] , $args[1] , $tag);
            }
        }else{
            if( $has_br ){
                return sprintf( "<%s%s/>\r\n" , $args[0], $args[1] );
            }else{
                return sprintf( "<%s%s/>" , $args[0], $args[1] );
            }
        }
    }
    public function html( $args ){
        return sprintf( 
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\r\n".'%s', 
            $this->tag( 'html' , $args , true , true ) );
    }
    public function head( $args ){
        return $this->tag( 'head' , $args , true , true );
    }
    public function body( $args ){
        return $this->tag( 'body' , $args , true , true );
    }
    public function metaCharset( $args ){
        return sprintf( '<meta http-equiv="Content-Type"  content="text/html; charset=%s" />' ,$args[0]);
    }
    public function metaIphone( $args ){
        return '<meta name="viewport" content="width=device-width, initial-scale=1.0,  maximum-scale=1.0, user-scalable=no" />';
    }
    public function loadCss( $args ){
        return sprintf(
            '<link href="%s" type="text/css" rel="stylesheet" />',
            $args[0]
        );
    }
    public function loadJs( $args ){
        return sprintf(
            '<script type="text/javascript" src="%s"></script>',
            $args[0]
        );        
    }
    public function br(){
        return "<br />";
    }
    public function hr(){
        return "<hr />";
    }
    public function img( $args ){
        return sprintf( '<img src="%s" %s />', $args[0] , $args[1] );
    }
    public function merge( $args ){
        return $this->core->execRaw( $args[0] );
    }
    public function extend( $args ){
        $file_name = $args[0];
        $res = $this->core->execRaw( $file_name );
        if( $res === false ){
            return null;
        }
        
        $this->blocks = array();
        $len = sizeof( $args );
        if( $len > 2 ){
        }
        $n = 1;
        while( isset( $args[$n+1] ) ){
            $this->blocks[ $args[$n] ] = $args[$n+1];
            $n+=2;
        }

        $this->core->extend_mode = true;
        $res = $this->core->execLib( $res );
        $this->core->extend_mode = false;
        
        return $res;
    }
    public function block( $args ){
        if( $this->core->extend_mode ){
            $name = $args[0];
            if( isset( $this->blocks[$name] ) ){
                return $this->core->execLib( $this->blocks[$name] );
            }
        }else{
            return null;
        }
    }
    public function comment( $args ){
        return sprintf("<!-- %s -->", implode( "\r\n" , $args ));
    }
}

class riseTemplate
{
    private $dir, $lib = array();
    public $set, $buffer = array() ,$result;
    public $extend_mode = false;
    public $args_separator = ',;';
    public $convert_strings = 
        array(
            '\!'=>'___EXCLAMATION___',
            '\{'=>'___START_TAG___',
            '\}'=>'___END_TAG___',
        );

    public function __construct(){
        $this->dir = dirname(__FILE__);
        $this->set = new stdClass;
    }
    public function setLibrary( $lib ){
        $this->lib = $lib;
        $this->lib->setCore( $this );
    }
    public function render( $file_name ){
        $buffer = $this->execRaw( $file_name );
        $this->buffer[] = $buffer;
        $res = $this->execLib( $buffer );
        $this->result = $res;
        return $res;
    }
    public function execRaw( $file_name ){
        if( !file_exists($file_name )){
            return false;
        }

        $set = $this->set;
        $time = filemtime( $file_name );
        ob_start();
        include( $file_name );
        $buffer = ob_get_clean();
        return $buffer;
    }

    public function execLib( $buffer ){

        $buffer = $this->convertEscapes( $buffer );

        do{
            $res = $buffer;
            $loop = true;
            $res = $this->useLib($res);
            if( $res !== false ){
                $buffer = $res;
            }else{
                $loop = false;
            }
        }while( $loop );

        $buffer = $this->revertEscapes( $buffer );

        return $buffer;
    }

    public function convertEscapes( $contents ){
        foreach( $this->convert_strings as $k=>$v ){
            $contents = str_replace( $k, $v , $contents );
        }
        return $contents;
    }

    public function revertEscapes( $contents ){
        foreach( $this->convert_strings as $k=>$v ){
            $contents = str_replace( $v, str_replace('\\','',$k) , $contents );
        }
        return $contents;
    }

    public function useLib($buffer){
        $lib_size = sizeof( $this->lib );
        $pattern = "/[ ]*\!([^-\{]*)\{([^\{\}]*)\}/";
        preg_match( $pattern , $buffer , $res );
        
        if( sizeof( $res ) == 0 ){
            return false;
        }
        
        $body = $res[0];
        $tag_name = $res[1];
        $tag_args = explode( $this->args_separator  ,$res[2] );

        $rev = array();
        foreach( $tag_args as $v ){
            $rev[] = $this->escape( $this->lib->replaceSets( trim( $v ) ) );
        }
        $tag_args = $rev;

        if( method_exists( $this->lib , $tag_name ) ){
            $rep = $this->lib->{$tag_name}( $tag_args );
        }else{
            $rep = $this->lib->tag( $tag_name , $tag_args );
        }
        
        $buffer_ = str_replace( $body , $rep , $buffer );
        return $buffer_;
    }

    public function escape( $contents ){
        do{ 
            $loop = true;
            $ret = $this->_esc( $contents );
            if( $ret == false ){
                $loop = false;
            }else{
                $contents = $ret;
            }
        }while( $loop );
        return $contents;
    }
    public function _esc( $contents ){
        $pattern = "/\/\*(.*?)\*\//";
        preg_match( $pattern , $contents , $res );
        if( empty( $res ) ){
            return false;
        }
        $contents = str_replace( $res[0] , $this->quoteEntity( $res[1] ) , $contents );

        return $contents;
    }
    public function quoteEntity( $c ){
        $c = str_replace('&' , '&amp;' , $c );
        $c = str_replace('<' , '&lt;' , $c );
        $c = str_replace('>' , '&gt;' , $c );
        $c = str_replace(' ' , '&nbsp;' , $c );
        $c = str_replace('"' , '&quot;' , $c );
        $c = str_replace('`' , '&acute;' , $c );
        $c = str_replace("'" , '&lsquo;' , $c );
        return $c;
    }
}