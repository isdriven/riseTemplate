!set{
    left20,;margin-left:20px,;
    left30,;margin-left:30px,;
}
!html{ null ,;
    !head{null ,;
        !metaCharset{UTF-8}
        !metaIphone{}
        !loadCss{reset.css}
        !loadJs{test.js}
    }
    !h1{,;riseTemplate is Tempalte Engine For PHP }
    !hr{}
    !h4{
        style="*left20*",;
        very simple,;
    }
    !h4{style="*left20*",;very extandable}
    !h4{style="*left20*",;very easy to read and write,;}
    !h4{style="*left20*",;support}
    !h4{style="*left30*",;constant value}
    !h4{style="*left30*",;merge}
    !h4{style="*left30*",;/*>*/extend\!,;
    }
    !h4{style="*left30*",;
        /*<escape>*/
    }
    !comment{
        ,;
        この部分はコメント,;
        何行書いてもOK,;
    }
    !div{style="height:30px;",;
        <?php
        for( $i = 0 ; $i < 20 ; ++$i ){
            printf("!div{style='float:left;padding:2px;border:1px solid #ccc;',;%s}\r\n",$i);
        }
        ?>
    }
    !block{ block_a }
    !merge{template2.php}
    !block{ block_b }
}
       
