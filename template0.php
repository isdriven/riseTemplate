!extend{template1.php,;
    block_a ,; 
    !div{style="height:30px;",;
        <?php
        for( $i = 0 ; $i < 20 ; ++$i ){
            printf("!div{style='float:left;padding:2px;border:1px solid #ccc;background-color:black;color:white;',;%s}\r\n",$i);
        }
        ?>
    } ,;
    block_b ,; 
    !div{style="height:30px;",;
        <?php
        for( $i = 0 ; $i < 20 ; ++$i ){
            printf("!div{style='float:left;padding:2px;border:1px solid #ccc;background-color:skyblue;color:green;',;%s}\r\n",$i);
        }
        ?>
    } ,;
}
