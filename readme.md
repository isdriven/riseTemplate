riseTemplate
======================
riseTemplate is simple template engine for php.  
riseTemplate は PHP のためのテンプレートエンジンです。

このテンプレートエンジンは、テンプレートファイルをなるべく快適に書き、読めるように設計されています。
以下のような、phpのモードでテンプレートを記述できますので、段落や色分けなど、phpと同一のモードで
非常に綺麗に整理することが出来ます。


    !html{ null ,;
        !head{null,;
            !metaCharset{UTF-8}
            !metaIphone{}
            !loadCss{reset.css}
            !loadJs{test.js}
        }
        !h1{,;riseTemplate is Tempalte Engine For PHP }
        !hr{}
        !h4{style="margin-left:20px;",;very simple}
        !h4{style="margin-left:20px;",;very extandable}
        !h4{style="margin-left:20px;",;very easy to read and write}
        <?php
        for( $i = 0 ; $i < 10 ; ++$i ){
            printf("!div{null,;%s}",$i);
        }
        ?>
    }

