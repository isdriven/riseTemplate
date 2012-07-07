<?php
/***
 * user riseTemplate
 */
include_once('riseTemplate.php');

$rise = new riseTemplate();
$lib = new riseTemplateLibrary();
$rise->setLibrary( $lib );

$rise->set->number = 100;

echo $rise->render( "template0.php" );

