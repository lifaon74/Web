<?php
require_once('AdvancedLanguage.php');

echo "result : \n\n";




$num_1 = new _Number(1);
$num_2 = new _Number(2);
$num_3 = new _Number(3);
//$c = new String($a);


$op_1 = new _Operation($num_1, '+', $num_2);
$op_2 = new _Operation($op_1, '/', $num_3);

$var_1 = new _Variable();
$var_1->set($num_1);

$op_3 = new _Operation($var_1, '+', $num_2);


$fnc_1 = new _Function();
$fnc_1->setInstructions([
	$arg_0 = $fnc_1->getArgument(0),
	$arg_1 = $fnc_1->getArgument(0),
]);

//print_r($op_3->optimize());

?>