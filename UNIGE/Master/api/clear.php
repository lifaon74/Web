<?php 
/**
	For debug only !
**/

if(!isset($fnc)) { require_once('../scripts/php/fnc.php'); }
require_once($fnc->absolutePath('scripts/php/APIFunctions.php'));

$debug = true;

if($debug) {
	//$APIFunctions->clearAll();
	$APIFunctions->initialize();
}

?>